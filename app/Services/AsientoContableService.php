<?php

namespace App\Services;

use App\Models\AsientoContable;
use App\Models\ConsumoMaterial;
use App\Models\CuentaContable;
use App\Models\CuentaFinanciera;
use App\Models\EntradaCompra;
use App\Models\Kardex;
use App\Models\TransaccionFinanciera;
use App\Models\Venta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AsientoContableService
{
    private const IGV = 0.18;

    public function registrarVenta(Venta $venta, int $usuarioId): AsientoContable
    {
        $venta->loadMissing('cliente', 'cuentaFinanciera');

        $total = (float) $venta->total;
        [$base, $igv] = $this->separarIgv($total);
        $cuentaDebe = $venta->condicion_pago === 'contado'
            ? $this->codigoCuentaFinanciera($venta->cuentaFinanciera)
            : '121';

        return $this->crearDesdeOperacionUnica(
            'Venta',
            $venta->id,
            $venta->fecha_venta,
            "Registro de venta {$venta->tipo_comprobante} {$venta->serie_comprobante}-{$venta->numero_comprobante}",
            $venta->moneda,
            $usuarioId,
            [
                ['codigo' => $cuentaDebe, 'tipo' => 'debe', 'monto' => $total, 'glosa' => 'Cliente: ' . ($venta->cliente->nombre ?? 'Sin cliente')],
                ['codigo' => '701', 'tipo' => 'haber', 'monto' => $base, 'glosa' => 'Base imponible de venta'],
                ['codigo' => '401', 'tipo' => 'haber', 'monto' => $igv, 'glosa' => 'IGV de venta'],
            ]
        );
    }

    public function registrarCostoVenta(Venta $venta, int $usuarioId): ?AsientoContable
    {
        $costo = Kardex::where('referencia_tipo', 'Venta')
            ->where('referencia_id', $venta->id)
            ->where('tipo_movimiento', Kardex::TIPO_SALIDA)
            ->get()
            ->sum(fn ($movimiento) => (float) $movimiento->cantidad * (float) $movimiento->precio_unitario);

        if ($costo <= 0) {
            return null;
        }

        return $this->crearDesdeOperacionUnica(
            'CostoVenta',
            $venta->id,
            $venta->fecha_venta,
            "Costo de venta {$venta->tipo_comprobante} {$venta->serie_comprobante}-{$venta->numero_comprobante}",
            $venta->moneda,
            $usuarioId,
            [
                ['codigo' => '691', 'tipo' => 'debe', 'monto' => $costo, 'glosa' => 'Reconocimiento del costo de venta'],
                ['codigo' => '20', 'tipo' => 'haber', 'monto' => $costo, 'glosa' => 'Salida valorizada de inventario'],
            ]
        );
    }

    public function registrarCompra(EntradaCompra $compra, int $usuarioId): AsientoContable
    {
        $compra->loadMissing('proveedor', 'detalles');

        $total = (float) $compra->detalles->sum('costo_total');
        [$base, $igv] = $this->separarIgv($total);

        return $this->crearDesdeOperacionUnica(
            'EntradaCompra',
            $compra->id,
            $compra->fecha_emision,
            "Registro de compra {$compra->numero_documento}",
            'PEN',
            $usuarioId,
            [
                ['codigo' => '601', 'tipo' => 'debe', 'monto' => $base, 'glosa' => 'Compra de materiales o mercaderias'],
                ['codigo' => '401', 'tipo' => 'debe', 'monto' => $igv, 'glosa' => 'Credito fiscal IGV'],
                ['codigo' => '421', 'tipo' => 'haber', 'monto' => $total, 'glosa' => 'Proveedor: ' . ($compra->proveedor->nombre_empresa ?? 'Sin proveedor')],
            ]
        );
    }

    public function registrarCobroVenta(Venta $venta, TransaccionFinanciera $transaccion): AsientoContable
    {
        $venta->loadMissing('cliente');
        $transaccion->loadMissing('cuenta');
        $codigoCajaBanco = $this->codigoCuentaFinanciera($transaccion->cuenta);
        $monto = (float) $transaccion->monto;

        return $this->crearDesdeOperacionUnica(
            'TransaccionFinanciera',
            $transaccion->id,
            $transaccion->fecha_transaccion,
            "Cobro de venta {$venta->tipo_comprobante} {$venta->serie_comprobante}-{$venta->numero_comprobante}",
            $venta->moneda,
            $transaccion->usuario_registra_id,
            [
                ['codigo' => $codigoCajaBanco, 'tipo' => 'debe', 'monto' => $monto, 'glosa' => 'Ingreso a ' . $transaccion->cuenta->nombre],
                ['codigo' => '121', 'tipo' => 'haber', 'monto' => $monto, 'glosa' => 'Cobro a cliente: ' . ($venta->cliente->nombre ?? 'Sin cliente')],
            ]
        );
    }

    public function registrarPagoCompra(EntradaCompra $compra, TransaccionFinanciera $transaccion): AsientoContable
    {
        $compra->loadMissing('proveedor');
        $transaccion->loadMissing('cuenta');
        $codigoCajaBanco = $this->codigoCuentaFinanciera($transaccion->cuenta);
        $monto = (float) $transaccion->monto;

        return $this->crearDesdeOperacionUnica(
            'TransaccionFinanciera',
            $transaccion->id,
            $transaccion->fecha_transaccion,
            "Pago de compra {$compra->numero_documento}",
            $transaccion->cuenta->moneda,
            $transaccion->usuario_registra_id,
            [
                ['codigo' => '421', 'tipo' => 'debe', 'monto' => $monto, 'glosa' => 'Pago a proveedor: ' . ($compra->proveedor->nombre_empresa ?? 'Sin proveedor')],
                ['codigo' => $codigoCajaBanco, 'tipo' => 'haber', 'monto' => $monto, 'glosa' => 'Salida de ' . $transaccion->cuenta->nombre],
            ]
        );
    }

    public function registrarMovimientoManual(TransaccionFinanciera $transaccion): ?AsientoContable
    {
        $transaccion->loadMissing('cuenta', 'cuentaContable');

        if (!$transaccion->cuenta_contable_id) {
            return null;
        }

        $codigoCajaBanco = $this->codigoCuentaFinanciera($transaccion->cuenta);
        $codigoContrapartida = $transaccion->cuentaContable->codigo;
        $monto = (float) $transaccion->monto;

        $lineas = $transaccion->tipo === 'ingreso'
            ? [
                ['codigo' => $codigoCajaBanco, 'tipo' => 'debe', 'monto' => $monto, 'glosa' => 'Ingreso a ' . $transaccion->cuenta->nombre],
                ['codigo' => $codigoContrapartida, 'tipo' => 'haber', 'monto' => $monto, 'glosa' => $transaccion->motivo],
            ]
            : [
                ['codigo' => $codigoContrapartida, 'tipo' => 'debe', 'monto' => $monto, 'glosa' => $transaccion->motivo],
                ['codigo' => $codigoCajaBanco, 'tipo' => 'haber', 'monto' => $monto, 'glosa' => 'Egreso de ' . $transaccion->cuenta->nombre],
            ];

        return $this->crearDesdeOperacionUnica(
            'TransaccionFinanciera',
            $transaccion->id,
            $transaccion->fecha_transaccion,
            $transaccion->motivo,
            $transaccion->cuenta->moneda,
            $transaccion->usuario_registra_id,
            $lineas
        );
    }

    public function registrarTransferencia(TransaccionFinanciera $salida, TransaccionFinanciera $entrada): AsientoContable
    {
        $salida->loadMissing('cuenta');
        $entrada->loadMissing('cuenta');
        $monto = (float) $salida->monto;

        return $this->crearDesdeOperacionUnica(
            'Transferencia',
            $salida->id,
            $salida->fecha_transaccion,
            'Transferencia entre cuentas financieras',
            $salida->cuenta->moneda,
            $salida->usuario_registra_id,
            [
                ['codigo' => $this->codigoCuentaFinanciera($entrada->cuenta), 'tipo' => 'debe', 'monto' => $monto, 'glosa' => 'Ingreso a ' . $entrada->cuenta->nombre],
                ['codigo' => $this->codigoCuentaFinanciera($salida->cuenta), 'tipo' => 'haber', 'monto' => $monto, 'glosa' => 'Salida de ' . $salida->cuenta->nombre],
            ]
        );
    }

    public function registrarConsumoProduccion(ConsumoMaterial $consumo, ?int $usuarioId): AsientoContable
    {
        $consumo->loadMissing('ordenProduccion', 'producto');
        $monto = (float) $consumo->costo_total;

        return $this->crearDesdeOperacionUnica(
            'ConsumoMaterial',
            $consumo->id,
            $consumo->created_at ?? now(),
            'Consumo de materiales en OP ' . $consumo->ordenProduccion->numero_orden,
            'PEN',
            $usuarioId,
            [
                ['codigo' => '61', 'tipo' => 'debe', 'monto' => $monto, 'glosa' => 'Consumo: ' . ($consumo->producto->nombre ?? 'Material')],
                ['codigo' => '24', 'tipo' => 'haber', 'monto' => $monto, 'glosa' => 'Salida de materias primas hacia produccion'],
            ]
        );
    }

    private function crearDesdeOperacionUnica(
        string $origenTipo,
        int $origenId,
        Carbon|string $fecha,
        string $descripcion,
        string $moneda,
        ?int $usuarioId,
        array $lineas
    ): AsientoContable {
        $existente = AsientoContable::where('origen_tipo', $origenTipo)
            ->where('origen_id', $origenId)
            ->first();

        if ($existente) {
            return $existente;
        }

        return $this->crearAsiento($fecha, $descripcion, $origenTipo, $origenId, $moneda, $usuarioId, $lineas);
    }

    private function crearAsiento(
        Carbon|string $fecha,
        string $descripcion,
        ?string $origenTipo,
        ?int $origenId,
        string $moneda,
        ?int $usuarioId,
        array $lineas
    ): AsientoContable {
        if (count($lineas) < 2) {
            throw new InvalidArgumentException('El asiento debe tener al menos dos lineas.');
        }

        $lineasPreparadas = $this->prepararLineas($lineas);
        $totalDebe = round(array_sum(array_column(array_filter($lineasPreparadas, fn ($linea) => $linea['tipo_movimiento'] === 'debe'), 'monto')), 2);
        $totalHaber = round(array_sum(array_column(array_filter($lineasPreparadas, fn ($linea) => $linea['tipo_movimiento'] === 'haber'), 'monto')), 2);

        if (abs($totalDebe - $totalHaber) > 0.01) {
            throw new InvalidArgumentException("El asiento no cuadra. Debe: {$totalDebe}, Haber: {$totalHaber}.");
        }

        return DB::transaction(function () use ($fecha, $descripcion, $origenTipo, $origenId, $moneda, $usuarioId, $lineasPreparadas, $totalDebe, $totalHaber) {
            $asiento = AsientoContable::create([
                'numero' => $this->generarNumero(),
                'fecha' => Carbon::parse($fecha)->toDateString(),
                'descripcion' => $descripcion,
                'origen_tipo' => $origenTipo,
                'origen_id' => $origenId,
                'moneda' => $moneda,
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'estado' => 'confirmado',
                'usuario_id' => $usuarioId,
            ]);

            $asiento->detalles()->createMany($lineasPreparadas);

            return $asiento->load('detalles.cuentaContable');
        });
    }

    private function prepararLineas(array $lineas): array
    {
        return collect($lineas)
            ->filter(fn ($linea) => round((float) $linea['monto'], 2) > 0)
            ->map(function ($linea) {
                $cuenta = CuentaContable::where('codigo', $linea['codigo'])->first();

                if (!$cuenta) {
                    throw new InvalidArgumentException("No existe la cuenta contable {$linea['codigo']}.");
                }

                return [
                    'cuenta_contable_id' => $cuenta->id,
                    'tipo_movimiento' => $linea['tipo'],
                    'monto' => round((float) $linea['monto'], 2),
                    'glosa' => $linea['glosa'] ?? null,
                ];
            })
            ->values()
            ->all();
    }

    private function separarIgv(float $total): array
    {
        $base = round($total / (1 + self::IGV), 2);
        $igv = round($total - $base, 2);

        return [$base, $igv];
    }

    private function codigoCuentaFinanciera(?CuentaFinanciera $cuenta): string
    {
        if (!$cuenta) {
            return '101';
        }

        return $cuenta->tipo === 'banco' ? '104' : '101';
    }

    private function generarNumero(): string
    {
        return 'ASI-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(5));
    }
}

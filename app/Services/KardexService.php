<?php

namespace App\Services;

use App\Models\Kardex;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Exception;

class KardexService
{
    /**
     * Registra un movimiento en el Kardex y actualiza el stock y precio del producto.
     * Calcula automáticamente el Promedio Ponderado para las Entradas.
     *
     * @param int $productoId
     * @param string $tipoMovimiento (entrada, salida, ajuste, devolucion)
     * @param float $cantidad
     * @param float|null $precioUnitario (obligatorio para entrada, nulo toma el actual para salida)
     * @param int $usuarioId
     * @param string|null $referenciaTipo
     * @param int|null $referenciaId
     * @param string|null $observaciones
     * @return Kardex
     * @throws Exception
     */
    public function registrarMovimiento(
        int $productoId,
        string $tipoMovimiento,
        float $cantidad,
        ?float $precioUnitario,
        int $usuarioId,
        ?string $referenciaTipo = null,
        ?int $referenciaId = null,
        ?string $observaciones = null
    ): Kardex {
        if ($cantidad <= 0) {
            throw new Exception("La cantidad debe ser mayor a cero.");
        }

        return DB::transaction(function () use (
            $productoId,
            $tipoMovimiento,
            $cantidad,
            $precioUnitario,
            $usuarioId,
            $referenciaTipo,
            $referenciaId,
            $observaciones
        ) {
            // Bloquea la fila del producto para evitar condiciones de carrera (concurrency)
            $producto = Producto::where('id', $productoId)->lockForUpdate()->firstOrFail();

            $saldoAnterior = (float) $producto->stock_actual;
            $precioActual = (float) $producto->precio_unitario;

            $saldoActual = $saldoAnterior;
            $nuevoPrecioUnitario = $precioActual;
            
            // Si es salida, el precio a usar es el costo promedio actual
            $precioAplicado = $precioUnitario ?? $precioActual;

            switch ($tipoMovimiento) {
                case Kardex::TIPO_ENTRADA:
                case Kardex::TIPO_DEVOLUCCION:
                    // Cálculo de Promedio Ponderado
                    $valorTotalAnterior = $saldoAnterior * $precioActual;
                    $valorIngreso = $cantidad * $precioAplicado;
                    
                    $saldoActual = $saldoAnterior + $cantidad;
                    
                    // Nuevo Precio Unitario = (Valor Anterior + Valor Ingreso) / Nuevo Saldo
                    if ($saldoActual > 0) {
                        $nuevoPrecioUnitario = ($valorTotalAnterior + $valorIngreso) / $saldoActual;
                    }
                    break;

                case Kardex::TIPO_SALIDA:
                    if ($saldoAnterior < $cantidad) {
                        throw new Exception("Stock insuficiente para realizar la salida. Stock actual: {$saldoAnterior}");
                    }
                    $saldoActual = $saldoAnterior - $cantidad;
                    // En las salidas el precio unitario promedio no cambia
                    $precioAplicado = $precioActual;
                    break;

                case Kardex::TIPO_AJUSTE:
                    // Ajuste puede ser positivo o negativo, lo manejamos simplificado:
                    // Si el usuario envia un precio, actualizamos el precio directamente.
                    $saldoActual = $saldoAnterior + $cantidad; 
                    if ($precioUnitario !== null) {
                        $nuevoPrecioUnitario = $precioUnitario;
                    }
                    break;
                    
                default:
                    throw new Exception("Tipo de movimiento no válido.");
            }

            // Redondeos por precisión
            $nuevoPrecioUnitario = round($nuevoPrecioUnitario, 2);
            $precioAplicado = round($precioAplicado, 2);

            // 1. Crear el registro en Kardex
            $kardex = Kardex::create([
                'producto_id' => $producto->id,
                'tipo_movimiento' => $tipoMovimiento,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioAplicado,
                'saldo_anterior' => $saldoAnterior,
                'saldo_actual' => $saldoActual,
                'referencia_tipo' => $referenciaTipo,
                'referencia_id' => $referenciaId,
                'usuario_id' => $usuarioId,
                'observaciones' => $observaciones,
                'fecha_movimiento' => now(),
            ]);

            // 2. Actualizar el producto
            $producto->update([
                'stock_actual' => $saldoActual,
                'precio_unitario' => $nuevoPrecioUnitario
            ]);

            return $kardex;
        });
    }
}

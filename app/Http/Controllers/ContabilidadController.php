<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Venta;
use App\Models\EntradaCompra;
use App\Models\TransaccionFinanciera;
use App\Models\AsientoContable;
use App\Models\DetalleAsientoContable;
use Carbon\Carbon;

class ContabilidadController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Manejo del Filtro de Fecha (Mes y Año)
        $mes = $request->input('mes', date('m'));
        $anio = $request->input('anio', date('Y'));
        
        $fechaInicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        // 2. Extraer Ventas Pagadas del Mes
        $ventas = Venta::with('cliente')
            ->where('estado', 'pagada')
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_venta')
            ->get();

        // 3. Extraer Compras (Entradas) del Mes
        $compras = EntradaCompra::with(['proveedor', 'detalles'])
            ->whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_emision')
            ->get();

        // 4. Extraer Movimientos de Tesorería del Mes
        $movimientosBancarios = TransaccionFinanciera::with('cuenta', 'usuarioRegistra')
            ->whereBetween('fecha_transaccion', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_transaccion')
            ->get();

        // 5. Cálculos para Totales del Mes
        // (Asumimos IGV 18% incluido en el total de ambas transacciones para el ejemplo)
        $totalVentas = $ventas->sum('total');
        $baseImponibleVentas = $totalVentas / 1.18;
        $igvVentas = $totalVentas - $baseImponibleVentas;

        $totalCompras = $compras->sum(function($compra) {
            return $compra->detalles->sum('costo_total');
        });
        $baseImponibleCompras = $totalCompras / 1.18;
        $igvCompras = $totalCompras - $baseImponibleCompras;

        return view('contabilidad.index', compact(
            'mes', 'anio', 'fechaInicio',
            'ventas', 'totalVentas', 'baseImponibleVentas', 'igvVentas',
            'compras', 'totalCompras', 'baseImponibleCompras', 'igvCompras',
            'movimientosBancarios'
        ));
    }

    public function planCuentas(): View
    {
        $cuentasPrincipales = \App\Models\CuentaContable::whereNull('padre_id')
            ->with('subcuentas')
            ->orderBy('codigo')
            ->get();

        return view('contabilidad.plan_cuentas', compact('cuentasPrincipales'));
    }

    public function libroDiario(Request $request): View
    {
        $mes = $request->input('mes', date('m'));
        $anio = $request->input('anio', date('Y'));

        $fechaInicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        $asientos = AsientoContable::with(['detalles.cuentaContable', 'usuario'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha')
            ->orderBy('numero')
            ->get();

        $totalDebe = $asientos->sum('total_debe');
        $totalHaber = $asientos->sum('total_haber');

        return view('contabilidad.libro_diario', compact(
            'mes',
            'anio',
            'fechaInicio',
            'asientos',
            'totalDebe',
            'totalHaber'
        ));
    }

    public function libroMayor(Request $request): View
    {
        $mes = $request->input('mes', date('m'));
        $anio = $request->input('anio', date('Y'));

        $fechaInicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        $detalles = DetalleAsientoContable::with(['cuentaContable', 'asiento'])
            ->whereHas('asiento', function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
            })
            ->get()
            ->sortBy(fn ($detalle) => $detalle->cuentaContable->codigo . '|'
                . $detalle->asiento->fecha->format('Ymd') . '|'
                . $detalle->asiento->numero);

        $cuentasMayor = $detalles
            ->groupBy('cuenta_contable_id')
            ->map(function ($movimientos) {
                $totalDebe = $movimientos
                    ->where('tipo_movimiento', 'debe')
                    ->sum('monto');
                $totalHaber = $movimientos
                    ->where('tipo_movimiento', 'haber')
                    ->sum('monto');

                return [
                    'cuenta' => $movimientos->first()->cuentaContable,
                    'movimientos' => $movimientos,
                    'totalDebe' => $totalDebe,
                    'totalHaber' => $totalHaber,
                    'saldo' => $totalDebe - $totalHaber,
                ];
            })
            ->sortBy(fn ($item) => $item['cuenta']->codigo);

        $totalDebe = $cuentasMayor->sum('totalDebe');
        $totalHaber = $cuentasMayor->sum('totalHaber');

        return view('contabilidad.libro_mayor', compact(
            'mes',
            'anio',
            'fechaInicio',
            'cuentasMayor',
            'totalDebe',
            'totalHaber'
        ));
    }

    public function balanceComprobacion(Request $request): View
    {
        $mes = $request->input('mes', date('m'));
        $anio = $request->input('anio', date('Y'));

        $fechaInicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fechaFin = $fechaInicio->copy()->endOfMonth();

        $detalles = DetalleAsientoContable::with(['cuentaContable', 'asiento'])
            ->whereHas('asiento', function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
            })
            ->get();

        $cuentasBalance = $detalles
            ->groupBy('cuenta_contable_id')
            ->map(function ($movimientos) {
                $totalDebe = $movimientos
                    ->where('tipo_movimiento', 'debe')
                    ->sum('monto');
                $totalHaber = $movimientos
                    ->where('tipo_movimiento', 'haber')
                    ->sum('monto');
                $saldo = $totalDebe - $totalHaber;

                return [
                    'cuenta' => $movimientos->first()->cuentaContable,
                    'totalDebe' => $totalDebe,
                    'totalHaber' => $totalHaber,
                    'saldoDeudor' => $saldo > 0 ? $saldo : 0,
                    'saldoAcreedor' => $saldo < 0 ? abs($saldo) : 0,
                ];
            })
            ->sortBy(fn ($item) => $item['cuenta']->codigo);

        $totalDebe = $cuentasBalance->sum('totalDebe');
        $totalHaber = $cuentasBalance->sum('totalHaber');
        $totalSaldoDeudor = $cuentasBalance->sum('saldoDeudor');
        $totalSaldoAcreedor = $cuentasBalance->sum('saldoAcreedor');

        return view('contabilidad.balance_comprobacion', compact(
            'mes',
            'anio',
            'fechaInicio',
            'cuentasBalance',
            'totalDebe',
            'totalHaber',
            'totalSaldoDeudor',
            'totalSaldoAcreedor'
        ));
    }
}

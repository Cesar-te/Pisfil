<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Venta;
use App\Models\EntradaCompra;
use App\Models\TransaccionFinanciera;
use App\Models\AsientoContable;
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
}

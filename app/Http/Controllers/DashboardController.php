<?php

namespace App\Http\Controllers;

use App\Models\AsientoContable;
use App\Models\EntradaCompra;
use App\Models\OrdenProduccion;
use App\Models\TareaProduccion;
use App\Models\Producto;
use App\Models\Kardex;
use App\Models\TransaccionFinanciera;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard principal
     */
    public function index(): View
    {
        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();
        $inicioMesAnterior = now()->subMonthNoOverflow()->startOfMonth();
        $finMesAnterior = now()->subMonthNoOverflow()->endOfMonth();

        $ventasMes = Venta::where('estado', 'pagada')
            ->whereBetween('fecha_venta', [$inicioMes, $finMes])
            ->sum('total');

        $ventasMesAnterior = Venta::where('estado', 'pagada')
            ->whereBetween('fecha_venta', [$inicioMesAnterior, $finMesAnterior])
            ->sum('total');

        $variacionVentas = $ventasMesAnterior > 0
            ? (($ventasMes - $ventasMesAnterior) / $ventasMesAnterior) * 100
            : null;

        $cuentasPorCobrar = Venta::where('estado_pago', '!=', 'pagado')
            ->selectRaw('SUM(total - monto_cobrado) as saldo')
            ->value('saldo') ?? 0;

        $clientesConDeuda = Venta::where('estado_pago', '!=', 'pagado')
            ->distinct('cliente_id')
            ->count('cliente_id');

        $cuentasPorPagar = EntradaCompra::where('estado_pago', '!=', 'pagado')
            ->withSum('detalles as total_compra', 'costo_total')
            ->get()
            ->sum(fn ($compra) => max(($compra->total_compra ?? 0) - $compra->monto_pagado, 0));

        $proveedoresConDeuda = EntradaCompra::where('estado_pago', '!=', 'pagado')
            ->distinct('proveedor_id')
            ->count('proveedor_id');

        $ingresosMes = TransaccionFinanciera::where('tipo', 'ingreso')
            ->whereBetween('fecha_transaccion', [$inicioMes, $finMes])
            ->sum('monto');

        $egresosMes = TransaccionFinanciera::where('tipo', 'egreso')
            ->whereBetween('fecha_transaccion', [$inicioMes, $finMes])
            ->sum('monto');

        $asientosMes = AsientoContable::whereBetween('fecha', [$inicioMes, $finMes])->count();

        // Estadísticas de órdenes de producción
        $ordenesTotales = OrdenProduccion::count();
        $ordenesEnProceso = OrdenProduccion::where('estado', 'en_proceso')->count();
        $ordenesCompletadas = OrdenProduccion::where('estado', 'completada')->count();

        // Estadísticas de tareas
        $tareasPendientes = TareaProduccion::where('estado', 'pendiente')->count();
        $tareasEnProgreso = TareaProduccion::where('estado', 'en_progreso')->count();

        // Estadísticas de inventario
        $productosActivos = Producto::where('estado', 'activo')->count();
        $productosStockBajo = Producto::whereRaw('stock_actual <= stock_minimo')
            ->where('estado', 'activo')
            ->count();

        $valorTotalInventario = Producto::where('estado', 'activo')
            ->selectRaw('SUM(stock_actual * precio_unitario) as total')
            ->value('total') ?? 0;

        // Órdenes próximas a vencer (en los próximos 7 días)
        $ordenesPorVencer = OrdenProduccion::where('estado', '!=', 'completada')
            ->whereDate('fecha_fin_planificada', '<=', now()->addDays(7))
            ->where('fecha_fin_planificada', '>', now())
            ->count();

        // Últimos movimientos de kardex
        $ultimosMovimientos = Kardex::with('producto', 'usuario')
            ->orderByDesc('fecha_movimiento')
            ->limit(5)
            ->get();

        // Tareas completadas esta semana
        $tareasCompletadasSemana = TareaProduccion::where('estado', 'completada')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $mesesLabels = [];
        $ventasChart = [];
        $comprasChart = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $start = $mes->copy()->startOfMonth();
            $end = $mes->copy()->endOfMonth();

            $mesesLabels[] = ucfirst($mes->translatedFormat('M Y'));
            $ventasChart[] = (float) Venta::where('estado', 'pagada')
                ->whereBetween('fecha_venta', [$start, $end])
                ->sum('total');
            $comprasChart[] = (float) EntradaCompra::whereBetween('fecha_emision', [$start, $end])
                ->withSum('detalles as total_compra', 'costo_total')
                ->get()
                ->sum('total_compra');
        }

        $productosAbc = Producto::where('estado', 'activo')
            ->select('id', 'nombre', 'stock_actual', 'precio_unitario')
            ->selectRaw('(stock_actual * precio_unitario) as valor_stock')
            ->orderByDesc('valor_stock')
            ->get();

        $totalValorAbc = $productosAbc->sum('valor_stock');
        $abcTotales = ['A' => 0, 'B' => 0, 'C' => 0];
        $acumulado = 0;

        foreach ($productosAbc as $producto) {
            $porcentaje = $totalValorAbc > 0 ? (($producto->valor_stock / $totalValorAbc) * 100) : 0;
            $acumulado += $porcentaje;
            $clase = $acumulado <= 80 ? 'A' : ($acumulado <= 95 ? 'B' : 'C');
            $abcTotales[$clase] += (float) $producto->valor_stock;
        }

        $abcSeries = array_values($abcTotales);
        $abcLabels = array_keys($abcTotales);

        $ultimosComprobantes = collect()
            ->merge(Venta::with('cliente')
                ->latest('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($venta) => [
                    'fecha' => $venta->created_at,
                    'documento' => trim(($venta->serie_comprobante ?? '') . '-' . ($venta->numero_comprobante ?? ''), '-'),
                    'tipo' => 'Venta',
                    'entidad' => $venta->cliente->nombre ?? 'Sin cliente',
                    'monto' => (float) $venta->total,
                    'estado' => $venta->estado_pago,
                ]))
            ->merge(EntradaCompra::with('proveedor')
                ->latest('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($compra) => [
                    'fecha' => $compra->created_at,
                    'documento' => $compra->numero_documento,
                    'tipo' => 'Compra',
                    'entidad' => $compra->proveedor->nombre_empresa ?? 'Sin proveedor',
                    'monto' => (float) $compra->detalles()->sum('costo_total'),
                    'estado' => $compra->estado_pago,
                ]))
            ->sortByDesc('fecha')
            ->take(6)
            ->values();

        return view('dashboard', compact(
            'ventasMes',
            'ventasMesAnterior',
            'variacionVentas',
            'cuentasPorCobrar',
            'clientesConDeuda',
            'cuentasPorPagar',
            'proveedoresConDeuda',
            'ingresosMes',
            'egresosMes',
            'asientosMes',
            'ordenesTotales',
            'ordenesEnProceso',
            'ordenesCompletadas',
            'tareasPendientes',
            'tareasEnProgreso',
            'productosActivos',
            'productosStockBajo',
            'valorTotalInventario',
            'ordenesPorVencer',
            'ultimosMovimientos',
            'tareasCompletadasSemana',
            'mesesLabels',
            'ventasChart',
            'comprasChart',
            'abcSeries',
            'abcLabels',
            'abcTotales',
            'ultimosComprobantes'
        ));
    }
}

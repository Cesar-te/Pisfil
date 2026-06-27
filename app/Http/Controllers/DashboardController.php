<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\TareaProduccion;
use App\Models\Producto;
use App\Models\Kardex;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard principal
     */
    public function index(): View
    {
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

        return view('dashboard', compact(
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
            'tareasCompletadasSemana'
        ));
    }
}

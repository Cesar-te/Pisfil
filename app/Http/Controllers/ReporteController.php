<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Venta;
use App\Models\TransaccionFinanciera;
use App\Models\Producto;
use App\Models\OrdenProduccion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function dashboard(): View
    {
        // 1. SALUD FINANCIERA (Ingresos vs Egresos del mes actual)
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $ingresosMes = TransaccionFinanciera::where('tipo', 'ingreso')
            ->whereBetween('fecha_transaccion', [$inicioMes, $finMes])
            ->sum('monto');
            
        $egresosMes = TransaccionFinanciera::where('tipo', 'egreso')
            ->whereBetween('fecha_transaccion', [$inicioMes, $finMes])
            ->sum('monto');

        // Para Gráfico: Últimos 6 meses de Ingresos y Egresos
        $mesesLabels = [];
        $ingresosChart = [];
        $egresosChart = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $mesesLabels[] = $mes->translatedFormat('M Y');
            
            $start = $mes->copy()->startOfMonth();
            $end = $mes->copy()->endOfMonth();

            $ingresosChart[] = TransaccionFinanciera::where('tipo', 'ingreso')->whereBetween('fecha_transaccion', [$start, $end])->sum('monto');
            $egresosChart[] = TransaccionFinanciera::where('tipo', 'egreso')->whereBetween('fecha_transaccion', [$start, $end])->sum('monto');
        }

        // 2. RENDIMIENTO DE VENTAS
        $ventasTotales = Venta::where('estado', 'pagada')->sum('total');
        $ventasMes = Venta::where('estado', 'pagada')->whereBetween('fecha_venta', [$inicioMes, $finMes])->sum('total');
        
        // Top 5 Productos más vendidos
        $topProductos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'), DB::raw('SUM(detalle_ventas.subtotal) as total_dinero'))
            ->where('ventas.estado', 'pagada')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // 3. VALORIZACIÓN DEL ALMACÉN (Stock * Costo Estimado/Promedio)
        $valorizacionAlmacen = Producto::where('estado', true)
            ->where('stock_actual', '>', 0)
            ->get()
            ->sum(function ($producto) {
                return $producto->stock_actual * $producto->costo_estimado;
            });

        // 4. EFICIENCIA DE PRODUCCIÓN
        $ordenesStats = [
            'Pendientes' => OrdenProduccion::where('estado', 'pendiente')->count(),
            'En Proceso' => OrdenProduccion::where('estado', 'en_proceso')->count(),
            'Completadas' => OrdenProduccion::where('estado', 'completada')->count(),
        ];

        return view('reportes.dashboard', compact(
            'ingresosMes', 'egresosMes', 'ventasTotales', 'ventasMes', 'valorizacionAlmacen',
            'mesesLabels', 'ingresosChart', 'egresosChart', 'topProductos', 'ordenesStats'
        ));
    }
}

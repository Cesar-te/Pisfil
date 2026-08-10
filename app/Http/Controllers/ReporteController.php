<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\EntradaCompra;
use App\Models\Kardex;
use App\Models\Venta;
use App\Models\TransaccionFinanciera;
use App\Models\Producto;
use App\Models\OrdenProduccion;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public function exportVentas(): StreamedResponse
    {
        $ventas = Venta::with('cliente')->orderBy('fecha_venta')->get();

        AuditoriaService::registrar('reporte.exportado', null, null, ['reporte' => 'ventas']);

        return response()->streamDownload(function () use ($ventas) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Fecha', 'Comprobante', 'Cliente', 'Documento', 'Condicion', 'Estado pago', 'Total'], ';');

            foreach ($ventas as $venta) {
                fputcsv($output, [
                    $venta->fecha_venta?->format('d/m/Y'),
                    trim($venta->tipo_comprobante . ' ' . $venta->serie_comprobante . '-' . $venta->numero_comprobante),
                    $venta->cliente->nombre ?? '',
                    $venta->cliente->documento_identidad ?? '',
                    $venta->condicion_pago,
                    $venta->estado_pago,
                    number_format((float) $venta->total, 2, '.', ''),
                ], ';');
            }

            fclose($output);
        }, 'reporte-ventas.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportCompras(): StreamedResponse
    {
        $compras = EntradaCompra::with(['proveedor', 'detalles'])->orderBy('fecha_emision')->get();

        AuditoriaService::registrar('reporte.exportado', null, null, ['reporte' => 'compras']);

        return response()->streamDownload(function () use ($compras) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Fecha', 'Documento', 'Proveedor', 'RUC', 'Estado', 'Estado pago', 'Total'], ';');

            foreach ($compras as $compra) {
                fputcsv($output, [
                    $compra->fecha_emision?->format('d/m/Y'),
                    $compra->numero_documento,
                    $compra->proveedor->nombre_empresa ?? '',
                    $compra->proveedor->ruc ?? '',
                    $compra->estado,
                    $compra->estado_pago,
                    number_format((float) $compra->detalles->sum('costo_total'), 2, '.', ''),
                ], ';');
            }

            fclose($output);
        }, 'reporte-compras.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportStock(): StreamedResponse
    {
        $productos = Producto::with(['categoria', 'unidadMedida'])->orderBy('nombre')->get();

        AuditoriaService::registrar('reporte.exportado', null, null, ['reporte' => 'stock']);

        return response()->streamDownload(function () use ($productos) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Codigo', 'Producto', 'Categoria', 'Unidad', 'Stock actual', 'Stock minimo', 'Costo unitario', 'Valor total', 'Estado'], ';');

            foreach ($productos as $producto) {
                $valor = (float) $producto->stock_actual * (float) $producto->precio_unitario;
                fputcsv($output, [
                    $producto->codigo,
                    $producto->nombre,
                    $producto->categoria->nombre ?? '',
                    $producto->unidadMedida->nombre ?? '',
                    number_format((float) $producto->stock_actual, 2, '.', ''),
                    number_format((float) $producto->stock_minimo, 2, '.', ''),
                    number_format((float) $producto->precio_unitario, 2, '.', ''),
                    number_format($valor, 2, '.', ''),
                    $producto->estado,
                ], ';');
            }

            fclose($output);
        }, 'reporte-stock.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportKardex(): StreamedResponse
    {
        $movimientos = Kardex::with(['producto', 'usuario'])->orderBy('fecha_movimiento')->get();

        AuditoriaService::registrar('reporte.exportado', null, null, ['reporte' => 'kardex']);

        return response()->streamDownload(function () use ($movimientos) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Fecha', 'Producto', 'Tipo', 'Cantidad', 'Costo unitario', 'Saldo anterior', 'Saldo actual', 'Referencia', 'Usuario'], ';');

            foreach ($movimientos as $movimiento) {
                fputcsv($output, [
                    $movimiento->fecha_movimiento?->format('d/m/Y H:i'),
                    $movimiento->producto->nombre ?? '',
                    $movimiento->tipo_movimiento,
                    number_format((float) $movimiento->cantidad, 2, '.', ''),
                    number_format((float) $movimiento->precio_unitario, 2, '.', ''),
                    number_format((float) $movimiento->saldo_anterior, 2, '.', ''),
                    number_format((float) $movimiento->saldo_actual, 2, '.', ''),
                    trim(($movimiento->referencia_tipo ?? '') . ' #' . ($movimiento->referencia_id ?? '')),
                    $movimiento->usuario->name ?? '',
                ], ';');
            }

            fclose($output);
        }, 'reporte-kardex.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportCaja(): StreamedResponse
    {
        $transacciones = TransaccionFinanciera::with(['cuenta', 'usuarioRegistra', 'cuentaContable'])
            ->orderBy('fecha_transaccion')
            ->get();

        AuditoriaService::registrar('reporte.exportado', null, null, ['reporte' => 'caja-bancos']);

        return response()->streamDownload(function () use ($transacciones) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Fecha', 'Cuenta', 'Tipo', 'Monto', 'Motivo', 'Referencia', 'Cuenta contable', 'Usuario'], ';');

            foreach ($transacciones as $transaccion) {
                fputcsv($output, [
                    $transaccion->fecha_transaccion?->format('d/m/Y'),
                    $transaccion->cuenta->nombre ?? '',
                    $transaccion->tipo,
                    number_format((float) $transaccion->monto, 2, '.', ''),
                    $transaccion->motivo,
                    $transaccion->referencia,
                    $transaccion->cuentaContable?->codigo,
                    $transaccion->usuarioRegistra->name ?? '',
                ], ';');
            }

            fclose($output);
        }, 'reporte-caja-bancos.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

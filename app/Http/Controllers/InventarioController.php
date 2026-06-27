<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Producto;
use App\Models\EntradaCompra;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

class InventarioController extends Controller
{
    /**
     * Mostrar dashboard de inventario
     */
    public function dashboard(): View
    {
        $stockBajo = Producto::whereRaw('stock_actual <= stock_minimo')
            ->where('estado', 'activo')
            ->count();

        $valorTotalInventario = Producto::where('estado', 'activo')
            ->selectRaw('SUM(stock_actual * precio_unitario) as total')
            ->value('total') ?? 0;

        $productosActivos = Producto::where('estado', 'activo')->count();
        
        $ultimosMovimientos = Kardex::with('producto', 'usuario')
            ->orderByDesc('fecha_movimiento')
            ->limit(10)
            ->get();

        return view('inventario.dashboard', compact(
            'stockBajo',
            'valorTotalInventario',
            'productosActivos',
            'ultimosMovimientos'
        ));
    }

    /**
     * Listar todos los productos con su estado de stock
     */
    public function productos(): View
    {
        $productos = Producto::with('categoria', 'unidadMedida')
            ->orderBy('nombre')
            ->paginate(20);

        return view('inventario.productos', compact('productos'));
    }

    /**
     * Ver movimientos de kardex
     */
    public function movimientosKardex(Request $request): View
    {
        $query = Kardex::with('producto', 'usuario');

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_movimiento', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_movimiento', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->orderByDesc('fecha_movimiento')->paginate(20);
        $productos = Producto::where('estado', 'activo')->pluck('nombre', 'id');

        return view('inventario.movimientos_kardex', compact('movimientos', 'productos'));
    }

    /**
     * Reporte de stock
     */
    public function reporteStock(): View
    {
        $productos = Producto::with('categoria', 'unidadMedida')
            ->where('estado', 'activo')
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();

        $totalValor = $productos->sum(fn($p) => $p->stock_actual * $p->precio_unitario);

        return view('inventario.reporte_stock', compact('productos', 'totalValor'));
    }

    /**
     * Productos con stock bajo
     */
    public function stockBajo(): View
    {
        $productos = Producto::whereRaw('stock_actual <= stock_minimo')
            ->where('estado', 'activo')
            ->with('categoria', 'unidadMedida')
            ->orderBy('stock_actual')
            ->paginate(20);

        return view('inventario.stock_bajo', compact('productos'));
    }

    /**
     * Productos con clasificación ABC
     */
    public function clasificacionABC(): View
    {
        $productos = Producto::where('estado', 'activo')
            ->selectRaw('*, (stock_actual * precio_unitario) as valor_stock')
            ->orderByDesc('valor_stock')
            ->get();

        $totalValor = $productos->sum('valor_stock');

        // Clasificar ABC
        $clasificados = [];
        $acumulado = 0;

        foreach ($productos as $producto) {
            $porcentaje = ($producto->valor_stock / $totalValor) * 100;
            $acumulado += $porcentaje;

            if ($acumulado <= 80) {
                $producto->clasificacion = 'A';
            } elseif ($acumulado <= 95) {
                $producto->clasificacion = 'B';
            } else {
                $producto->clasificacion = 'C';
            }
        }

        return view('inventario.clasificacion_abc', compact('productos'));
    }
}

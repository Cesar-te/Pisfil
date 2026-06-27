<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Producto;
use App\Models\EntradaCompra;
use App\Services\KardexService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Formulario para registrar movimiento manual
     */
    public function createMovimiento(): View
    {
        $productos = Producto::where('estado', 'activo')->orderBy('nombre')->get();
        return view('inventario.create_movimiento', compact('productos'));
    }

    /**
     * Guardar un movimiento manual en Kardex
     */
    public function storeMovimiento(Request $request, KardexService $kardexService): RedirectResponse
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo_movimiento' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|numeric|min:0.01',
            'precio_unitario' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            // Validar que en entrada el precio sea obligatorio (en nuestra lógica de Ajuste o Entrada)
            if ($validated['tipo_movimiento'] === Kardex::TIPO_ENTRADA && !isset($validated['precio_unitario'])) {
                return back()->withInput()->withErrors(['precio_unitario' => 'El precio unitario es obligatorio para entradas.']);
            }

            $kardexService->registrarMovimiento(
                $validated['producto_id'],
                $validated['tipo_movimiento'],
                $validated['cantidad'],
                $validated['precio_unitario'] ?? null,
                auth()->id(),
                'Ajuste Manual', // Referencia tipo genérica
                null, // Referencia ID
                $validated['observaciones']
            );

            return redirect()->route('inventario.movimientos_kardex')
                ->with('success', 'Movimiento registrado correctamente en el Kárdex.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

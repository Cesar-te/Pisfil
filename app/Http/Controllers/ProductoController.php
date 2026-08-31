<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductoController extends Controller
{
    /**
     * Mostrar lista de productos
     */
    public function index(): View
    {
        $productos = Producto::with(['categoria', 'unidadMedida'])
            ->withExists('movimientosKardex')
            ->orderBy('nombre')
            ->paginate(15);
        $categorias = \App\Models\Categoria::where('estado', true)->orderBy('nombre')->get();
        $unidades = \App\Models\UnidadMedida::where('estado', true)->orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'unidades'));
    }

    /**
     * Mostrar formulario para crear producto
     */
    public function create(): View
    {
        $categorias = \App\Models\Categoria::where('estado', true)->get();
        $unidades = \App\Models\UnidadMedida::where('estado', true)->get();

        return view('productos.create', compact('categorias', 'unidades'));
    }

    /**
     * Guardar nuevo producto
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'precio_unitario' => 'required|numeric|min:0',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'estado' => 'nullable|string',
        ]);

        $producto = Producto::create($validated);
        AuditoriaService::registrar('producto.creado', $producto, null, $producto->toArray());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');
    }

    /**
     * Mostrar detalles del producto
     */
    public function show(Producto $producto): View
    {
        $producto->load(['categoria', 'unidadMedida', 'movimientosKardex']);

        return view('productos.show', compact('producto'));
    }

    /**
     * Mostrar formulario para editar producto
     */
    public function edit(Producto $producto): View
    {
        $categorias = \App\Models\Categoria::where('estado', true)->get();
        $unidades = \App\Models\UnidadMedida::where('estado', true)->get();

        return view('productos.edit', compact('producto', 'categorias', 'unidades'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $producto->id,
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'precio_unitario' => 'required|numeric|min:0',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'estado' => 'nullable|string',
        ]);

        if ($producto->movimientosKardex()->exists()) {
            unset($validated['stock_actual']);
        }

        $antes = $producto->only(array_keys($validated));
        $producto->update($validated);
        AuditoriaService::registrar('producto.actualizado', $producto, $antes, $producto->fresh()->only(array_keys($validated)));

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Eliminar producto
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        $antes = $producto->toArray();
        $producto->delete();
        AuditoriaService::registrar('producto.eliminado', $producto, $antes, null);

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }

    /**
     * Obtener productos activos (para AJAX)
     */
    public function activos()
    {
        return response()->json(
            Producto::where('estado', 'activo')
                ->select('id', 'codigo', 'nombre', 'stock_actual', 'precio_unitario')
                ->orderBy('nombre')
                ->get()
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\EntradaCompra;
use App\Models\DetalleEntradaCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EntradaCompraController extends Controller
{
    /**
     * Mostrar lista de entradas de compra
     */
    public function index(): View
    {
        $entradas = EntradaCompra::with('proveedor', 'usuario')
            ->orderByDesc('fecha_emision')
            ->paginate(15);

        return view('entradas_compra.index', compact('entradas'));
    }

    /**
     * Mostrar formulario para crear entrada
     */
    public function create(): View
    {
        $proveedores = Proveedor::where('estado', true)->get();

        return view('entradas_compra.create', compact('proveedores'));
    }

    /**
     * Guardar nueva entrada
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero_documento' => 'required|string|max:50|unique:entradas_compra',
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_emision' => 'required|date',
        ]);

        $validated['usuario_id'] = Auth::id();
        $validated['estado'] = 'pendiente';

        EntradaCompra::create($validated);

        return redirect()->route('entradas-compra.index')
            ->with('success', 'Entrada de compra creada exitosamente');
    }

    /**
     * Mostrar detalles de la entrada
     */
    public function show(EntradaCompra $entradaCompra): View
    {
        $entradaCompra->load('detalles.producto', 'proveedor', 'usuario');

        return view('entradas_compra.show', compact('entradaCompra'));
    }

    /**
     * Mostrar formulario para editar entrada
     */
    public function edit(EntradaCompra $entradaCompra): View
    {
        $proveedores = Proveedor::where('estado', true)->get();

        return view('entradas_compra.edit', compact('entradaCompra', 'proveedores'));
    }

    /**
     * Actualizar entrada
     */
    public function update(Request $request, EntradaCompra $entradaCompra): RedirectResponse
    {
        $validated = $request->validate([
            'numero_documento' => 'required|string|max:50|unique:entradas_compra,numero_documento,' . $entradaCompra->id,
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:pendiente,recibida,validada,rechazada',
        ]);

        $entradaCompra->update($validated);

        return redirect()->route('entradas-compra.index')
            ->with('success', 'Entrada actualizada exitosamente');
    }

    /**
     * Cambiar estado de la entrada
     */
    public function cambiarEstado(Request $request, EntradaCompra $entradaCompra): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:pendiente,recibida,validada,rechazada',
        ]);

        $entradaCompra->update($validated);

        return back()->with('success', 'Estado actualizado');
    }

    /**
     * Eliminar entrada
     */
    public function destroy(EntradaCompra $entradaCompra): RedirectResponse
    {
        $entradaCompra->delete();

        return redirect()->route('entradas-compra.index')
            ->with('success', 'Entrada eliminada exitosamente');
    }

    /**
     * Agregar detalle a entrada (AJAX)
     */
    public function agregarDetalle(Request $request, EntradaCompra $entradaCompra)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_solicitada' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);
        $costo_total = $validated['cantidad_solicitada'] * $validated['precio_unitario'];

        $detalle = DetalleEntradaCompra::create([
            'entrada_compra_id' => $entradaCompra->id,
            'producto_id' => $validated['producto_id'],
            'cantidad_solicitada' => $validated['cantidad_solicitada'],
            'precio_unitario' => $validated['precio_unitario'],
            'costo_total' => $costo_total,
        ]);

        return response()->json([
            'success' => true,
            'detalle' => $detalle->load('producto'),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProveedorController extends Controller
{
    /**
     * Mostrar lista de proveedores
     */
    public function index(): View
    {
        $proveedores = Proveedor::orderBy('nombre_empresa')->paginate(15);

        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Mostrar formulario para crear proveedor
     */
    public function create(): View
    {
        return view('proveedores.create');
    }

    /**
     * Guardar nuevo proveedor
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:proveedores',
            'nombre_empresa' => 'required|string|max:150',
            'nombre_contacto' => 'nullable|string|max:100',
            'documento_identidad' => 'nullable|string|max:30',
            'ruc' => 'nullable|string|max:15|unique:proveedores,ruc',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'condicion_pago' => 'nullable|string|max:50',
            'plazo_entrega' => 'nullable|integer|min:0',
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    /**
     * Mostrar detalles del proveedor
     */
    public function show(Proveedor $proveedor): View
    {
        $proveedor->load('entradas');

        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Mostrar formulario para editar proveedor
     */
    public function edit(Proveedor $proveedor): View
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Actualizar proveedor
     */
    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:proveedores,codigo,' . $proveedor->id,
            'nombre_empresa' => 'required|string|max:150',
            'nombre_contacto' => 'nullable|string|max:100',
            'documento_identidad' => 'nullable|string|max:30',
            'ruc' => 'nullable|string|max:15|unique:proveedores,ruc,' . $proveedor->id,
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'condicion_pago' => 'nullable|string|max:50',
            'plazo_entrega' => 'nullable|integer|min:0',
            'estado' => 'nullable|boolean',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    /**
     * Eliminar proveedor
     */
    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente');
    }

    /**
     * Obtener proveedores activos (para AJAX)
     */
    public function activos()
    {
        return response()->json(
            Proveedor::where('estado', true)
                ->select('id', 'codigo', 'nombre_empresa', 'email', 'telefono')
                ->orderBy('nombre_empresa')
                ->get()
        );
    }
}

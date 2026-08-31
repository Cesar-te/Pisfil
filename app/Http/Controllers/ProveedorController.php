<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

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
            'codigo' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('proveedores')],
            'nombre_empresa' => 'required|string|max:150',
            'nombre_contacto' => 'nullable|string|max:100',
            'documento_identidad' => ['nullable', 'digits:8'],
            'ruc' => ['nullable', 'digits:11', Rule::unique('proveedores', 'ruc')],
            'email' => 'nullable|email',
            'telefono' => ['nullable', 'regex:/^[0-9+()\s-]{6,20}$/'],
            'celular' => ['nullable', 'regex:/^[0-9+()\s-]{6,20}$/'],
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'condicion_pago' => 'nullable|string|max:50',
            'plazo_entrega' => 'nullable|integer|min:0',
        ]);

        $proveedor = Proveedor::create($validated);
        AuditoriaService::registrar('proveedor.creado', $proveedor, null, $proveedor->toArray());

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
            'codigo' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique('proveedores', 'codigo')->ignore($proveedor->id)],
            'nombre_empresa' => 'required|string|max:150',
            'nombre_contacto' => 'nullable|string|max:100',
            'documento_identidad' => ['nullable', 'digits:8'],
            'ruc' => ['nullable', 'digits:11', Rule::unique('proveedores', 'ruc')->ignore($proveedor->id)],
            'email' => 'nullable|email',
            'telefono' => ['nullable', 'regex:/^[0-9+()\s-]{6,20}$/'],
            'celular' => ['nullable', 'regex:/^[0-9+()\s-]{6,20}$/'],
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'condicion_pago' => 'nullable|string|max:50',
            'plazo_entrega' => 'nullable|integer|min:0',
            'estado' => 'nullable|boolean',
        ]);

        $antes = $proveedor->only(array_keys($validated));
        $proveedor->update($validated);
        AuditoriaService::registrar('proveedor.actualizado', $proveedor, $antes, $proveedor->fresh()->only(array_keys($validated)));

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    /**
     * Eliminar proveedor
     */
    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $antes = $proveedor->toArray();
        $proveedor->delete();
        AuditoriaService::registrar('proveedor.eliminado', $proveedor, $antes, null);

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

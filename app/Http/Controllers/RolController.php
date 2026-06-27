<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Rol::all();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modulos = [
            'dashboard' => 'Dashboard',
            'usuarios' => 'Gestión de Usuarios',
            'roles' => 'Gestión de Roles',
            'inventario' => 'Módulo de Inventario',
            'compras' => 'Módulo de Compras',
            'ventas' => 'Módulo de Ventas',
            'produccion' => 'Módulo de Producción',
        ];
        return view('roles.create', compact('modulos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:50|unique:roles',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $permisos = $request->input('permisos', []);

        Rol::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado' => $request->has('estado') ? true : false,
            'permisos_json' => $permisos
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rol $role)
    {
        $modulos = [
            'dashboard' => 'Dashboard',
            'usuarios' => 'Gestión de Usuarios',
            'roles' => 'Gestión de Roles',
            'inventario' => 'Módulo de Inventario',
            'compras' => 'Módulo de Compras',
            'ventas' => 'Módulo de Ventas',
            'produccion' => 'Módulo de Producción',
        ];
        return view('roles.edit', compact('role', 'modulos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rol $role)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:50|unique:roles,codigo,' . $role->id,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $permisos = $request->input('permisos', []);

        $role->update([
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado' => $request->has('estado') ? true : false,
            'permisos_json' => $permisos
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rol $role)
    {
        // En lugar de eliminar, podríamos desactivar o eliminar si no tiene usuarios
        if ($role->usuarios()->exists()) {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente.');
    }
}

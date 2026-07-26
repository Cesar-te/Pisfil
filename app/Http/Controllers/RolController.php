<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RolController extends Controller
{
    // Eliminado: const PERMISOS_SISTEMA ya que ahora vienen de la BD

    public function index(): View
    {
        $roles = Rol::withCount('usuarios')->orderBy('nombre')->paginate(15);
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permisosDisponibles = Permiso::pluck('descripcion', 'codigo')->toArray();
        return view('roles.create', compact('permisosDisponibles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
            'permisos' => 'nullable|array',
            'permiso_total' => 'nullable|boolean'
        ]);

        if ($request->has('permiso_total') && $request->permiso_total == '1') {
            $permisosJson = ['*'];
        } else {
            $permisosJson = $request->input('permisos', []);
        }

        $codigo = strtolower(str_replace(' ', '_', $validated['nombre']));

        $rol = Rol::create([
            'codigo' => $codigo,
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'estado' => $validated['estado'],
        ]);

        $permisoIds = Permiso::whereIn('codigo', $permisosJson)->pluck('id')->toArray();
        $rol->permisos()->sync($permisoIds);

        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function edit(Rol $role): View
    {
        $permisosDisponibles = Permiso::pluck('descripcion', 'codigo')->toArray();
        // Cargar permisos actuales para la vista
        $role->load('permisos'); 
        return view('roles.edit', ['rol' => $role, 'permisosDisponibles' => $permisosDisponibles]);
    }

    public function update(Request $request, Rol $role): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
            'permisos' => 'nullable|array',
            'permiso_total' => 'nullable|boolean'
        ]);

        if ($request->has('permiso_total') && $request->permiso_total == '1') {
            $permisosJson = ['*'];
        } else {
            $permisosJson = $request->input('permisos', []);
        }

        $role->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'estado' => $validated['estado'],
        ]);

        $permisoIds = Permiso::whereIn('codigo', $permisosJson)->pluck('id')->toArray();
        $role->permisos()->sync($permisoIds);

        return redirect()->route('roles.index')->with('success', 'Rol y permisos actualizados correctamente.');
    }
}

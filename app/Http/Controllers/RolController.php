<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RolController extends Controller
{
    // Lista de todos los permisos disponibles en el sistema (Podría venir de config en un sistema más grande)
    const PERMISOS_SISTEMA = [
        'dashboard' => 'Ver Dashboard General',
        'inventario' => 'Acceso a Módulo de Inventario y Kárdex',
        'compras' => 'Acceso a Módulo de Compras',
        'ventas' => 'Acceso a Módulo de Ventas',
        'produccion' => 'Acceso a Módulo de Producción (Órdenes)',
        'reportes' => 'Ver Reportes Gerenciales',
        'contabilidad' => 'Acceso a Módulo de Contabilidad',
        'usuarios' => 'Gestionar Usuarios',
        'roles' => 'Gestionar Roles y Permisos',
    ];

    public function index(): View
    {
        $roles = Rol::withCount('usuarios')->orderBy('nombre')->paginate(15);
        return view('roles.index', compact('roles'));
    }

    public function edit(Rol $rol): View
    {
        $permisosDisponibles = self::PERMISOS_SISTEMA;
        return view('roles.edit', compact('rol', 'permisosDisponibles'));
    }

    public function update(Request $request, Rol $rol): RedirectResponse
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

        $rol->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'estado' => $validated['estado'],
            'permisos_json' => json_encode($permisosJson)
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol y permisos actualizados correctamente.');
    }
}

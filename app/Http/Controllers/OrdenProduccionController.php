<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrdenProduccionController extends Controller
{
    /**
     * Mostrar lista de órdenes de producción
     */
    public function index(): View
    {
        $ordenes = OrdenProduccion::with(['usuarioCreador', 'usuarioAsignado'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('ordenes_produccion.index', compact('ordenes'));
    }

    /**
     * Mostrar formulario para crear orden
     */
    public function create(): View
    {
        $usuarios = User::where('estado', true)
            ->whereHas('rol', function($q) {
                $q->whereIn('codigo', ['gerente', 'operario']);
            })
            ->get();

        return view('ordenes_produccion.create', compact('usuarios'));
    }

    /**
     * Guardar nueva orden
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero_orden' => 'required|string|max:50|unique:ordenes_produccion',
            'cliente' => 'required|string|max:150',
            'descripcion_trabajo' => 'required|string',
            'fecha_inicio_planificada' => 'required|date',
            'fecha_fin_planificada' => 'required|date|after:fecha_inicio_planificada',
            'usuario_asignado_id' => 'nullable|exists:users,id',
        ]);

        $validated['usuario_creador_id'] = Auth::id();
        $validated['estado'] = 'planificada';

        OrdenProduccion::create($validated);

        return redirect()->route('ordenes-produccion.index')
            ->with('success', 'Orden de producción creada exitosamente');
    }

    /**
     * Mostrar detalles de la orden
     */
    public function show(OrdenProduccion $ordenProduccion): View
    {
        $ordenProduccion->load([
            'usuarioCreador',
            'usuarioAsignado',
            'tareas',
            'consumoMateriales.producto',
        ]);

        return view('ordenes_produccion.show', compact('ordenProduccion'));
    }

    /**
     * Mostrar formulario para editar orden
     */
    public function edit(OrdenProduccion $ordenProduccion): View
    {
        $usuarios = User::where('estado', true)->get();

        return view('ordenes_produccion.edit', compact('ordenProduccion', 'usuarios'));
    }

    /**
     * Actualizar orden
     */
    public function update(Request $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'numero_orden' => 'required|string|max:50|unique:ordenes_produccion,numero_orden,' . $ordenProduccion->id,
            'cliente' => 'required|string|max:150',
            'descripcion_trabajo' => 'required|string',
            'estado' => 'required|in:planificada,en_proceso,pausada,completada,cancelada',
            'fecha_inicio_planificada' => 'required|date',
            'fecha_fin_planificada' => 'required|date|after:fecha_inicio_planificada',
            'usuario_asignado_id' => 'nullable|exists:users,id',
        ]);

        $ordenProduccion->update($validated);

        return redirect()->route('ordenes-produccion.index')
            ->with('success', 'Orden actualizada exitosamente');
    }

    /**
     * Cambiar estado de la orden
     */
    public function cambiarEstado(Request $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:planificada,en_proceso,pausada,completada,cancelada',
        ]);

        $ordenProduccion->update($validated);

        return back()->with('success', 'Estado actualizado');
    }

    /**
     * Eliminar orden
     */
    public function destroy(OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $ordenProduccion->delete();

        return redirect()->route('ordenes-produccion.index')
            ->with('success', 'Orden eliminada exitosamente');
    }
}

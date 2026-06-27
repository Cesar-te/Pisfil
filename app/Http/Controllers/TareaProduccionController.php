<?php

namespace App\Http\Controllers;

use App\Models\TareaProduccion;
use App\Models\OrdenProduccion;
use App\Models\ProcesoProduccion;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TareaProduccionController extends Controller
{
    /**
     * Mostrar lista de tareas
     */
    public function index(): View
    {
        $tareas = TareaProduccion::with(['ordenProduccion', 'usuarioResponsable', 'proceso'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('tareas_produccion.index', compact('tareas'));
    }

    /**
     * Mostrar formulario para crear tarea
     */
    public function create(Request $request): View
    {
        $orden_id = $request->query('orden_id');
        $orden = $orden_id ? OrdenProduccion::findOrFail($orden_id) : null;
        $procesos = ProcesoProduccion::where('estado', true)->get();
        $usuarios = \App\Models\User::where('estado', true)->get();

        return view('tareas_produccion.create', compact('orden', 'procesos', 'usuarios'));
    }

    /**
     * Guardar nueva tarea
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orden_produccion_id' => 'required|exists:ordenes_produccion,id',
            'numero_tarea' => 'required|string|max:50',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'proceso_produccion_id' => 'nullable|exists:procesos_produccion,id',
            'fecha_inicio_planificada' => 'required|date',
            'fecha_fin_planificada' => 'required|date|after:fecha_inicio_planificada',
            'usuario_responsable_id' => 'required|exists:users,id',
        ]);

        $validated['estado'] = 'pendiente';
        $validated['porcentaje_avance'] = 0;

        TareaProduccion::create($validated);

        return redirect()->route('tareas-produccion.index')
            ->with('success', 'Tarea creada exitosamente');
    }

    /**
     * Mostrar detalles de la tarea
     */
    public function show(TareaProduccion $tareaProduccion): View
    {
        $tareaProduccion->load([
            'ordenProduccion',
            'usuarioResponsable',
            'proceso',
            'incidencias',
            'reportes',
        ]);

        return view('tareas_produccion.show', compact('tareaProduccion'));
    }

    /**
     * Mostrar formulario para editar tarea
     */
    public function edit(TareaProduccion $tareaProduccion): View
    {
        $procesos = ProcesoProduccion::where('estado', true)->get();
        $usuarios = \App\Models\User::where('estado', true)->get();

        return view('tareas_produccion.edit', compact('tareaProduccion', 'procesos', 'usuarios'));
    }

    /**
     * Actualizar tarea
     */
    public function update(Request $request, TareaProduccion $tareaProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'proceso_produccion_id' => 'nullable|exists:procesos_produccion,id',
            'estado' => 'required|in:pendiente,en_progreso,completada,reproceso',
            'fecha_inicio_planificada' => 'required|date',
            'fecha_fin_planificada' => 'required|date|after:fecha_inicio_planificada',
            'usuario_responsable_id' => 'required|exists:users,id',
            'porcentaje_avance' => 'required|integer|min:0|max:100',
        ]);

        $tareaProduccion->update($validated);

        return redirect()->route('tareas-produccion.index')
            ->with('success', 'Tarea actualizada exitosamente');
    }

    /**
     * Actualizar porcentaje de avance
     */
    public function actualizarAvance(Request $request, TareaProduccion $tareaProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'porcentaje_avance' => 'required|integer|min:0|max:100',
            'observaciones' => 'nullable|string',
        ]);

        $tareaProduccion->update($validated);

        return back()->with('success', 'Avance actualizado');
    }

    /**
     * Cambiar estado de la tarea
     */
    public function cambiarEstado(Request $request, TareaProduccion $tareaProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completada,reproceso',
        ]);

        $tareaProduccion->update($validated);

        return back()->with('success', 'Estado actualizado');
    }

    /**
     * Eliminar tarea
     */
    public function destroy(TareaProduccion $tareaProduccion): RedirectResponse
    {
        $tareaProduccion->delete();

        return redirect()->route('tareas-produccion.index')
            ->with('success', 'Tarea eliminada exitosamente');
    }
}

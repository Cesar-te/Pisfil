<?php

namespace App\Http\Controllers;

use App\Models\TareaProduccion;
use App\Models\OrdenProduccion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class TareaProduccionController extends Controller
{
    public function store(Request $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'numero_tarea' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                \Illuminate\Validation\Rule::unique('tareas_produccion')->where(function ($query) use ($ordenProduccion) {
                    return $query->where('orden_produccion_id', $ordenProduccion->id);
                })
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'proceso_produccion_id' => 'required|exists:procesos_produccion,id',
            'fecha_inicio_planificada' => 'required|date',
            'fecha_fin_planificada' => 'required|date|after_or_equal:fecha_inicio_planificada',
            'usuario_responsable_id' => 'required|exists:users,id',
        ]);

        $validated['orden_produccion_id'] = $ordenProduccion->id;
        $validated['estado'] = TareaProduccion::ESTADO_PENDIENTE;
        $validated['porcentaje_avance'] = 0;

        TareaProduccion::create($validated);

        return back()->with('success', 'Tarea asignada exitosamente a la orden.');
    }

    public function updateAvance(Request $request, TareaProduccion $tareaProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'porcentaje_avance' => 'required|integer|min:0|max:100',
        ]);

        $estado = $tareaProduccion->estado;
        if ($validated['porcentaje_avance'] == 100) {
            $estado = TareaProduccion::ESTADO_COMPLETADA;
            if (!$tareaProduccion->fecha_fin_real) $tareaProduccion->fecha_fin_real = now();
        } elseif ($validated['porcentaje_avance'] > 0) {
            $estado = TareaProduccion::ESTADO_EN_PROGRESO;
            if (!$tareaProduccion->fecha_inicio_real) $tareaProduccion->fecha_inicio_real = now();
        }

        $tareaProduccion->update([
            'porcentaje_avance' => $validated['porcentaje_avance'],
            'estado' => $estado
        ]);

        return back()->with('success', 'Avance de la tarea actualizado.');
    }
}

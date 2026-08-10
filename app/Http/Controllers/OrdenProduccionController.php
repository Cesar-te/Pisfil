<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\User;
use App\Models\ProcesoProduccion;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrdenProduccionController extends Controller
{
    public function index(): View
    {
        $ordenes = OrdenProduccion::with('usuarioCreador', 'usuarioAsignado')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('produccion.ordenes.index', compact('ordenes'));
    }

    public function create(): View
    {
        // Usuarios con rol de operario o gerente
        $usuarios = User::where('estado', true)->get();
        return view('produccion.ordenes.create', compact('usuarios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero_orden' => 'required|string|max:50|unique:ordenes_produccion',
            'cliente' => 'required|string|max:255',
            'descripcion_trabajo' => 'required|string',
            'fecha_inicio_planificada' => 'required|date',
            'fecha_fin_planificada' => 'required|date|after_or_equal:fecha_inicio_planificada',
            'usuario_asignado_id' => 'nullable|exists:users,id',
        ]);

        $validated['usuario_creador_id'] = Auth::id();
        $validated['estado'] = OrdenProduccion::ESTADO_PLANIFICADA;

        $orden = OrdenProduccion::create($validated);
        AuditoriaService::registrar('produccion.orden_creada', $orden, null, $orden->toArray());

        return redirect()->route('ordenes-produccion.index')
            ->with('success', 'Orden de Producción creada exitosamente');
    }

    public function show(OrdenProduccion $ordenProduccion): View
    {
        $ordenProduccion->load(['tareas.usuarioResponsable', 'tareas.proceso', 'consumoMateriales.producto', 'usuarioCreador', 'usuarioAsignado']);
        
        $usuarios = User::where('estado', true)->get();
        $procesos = ProcesoProduccion::where('estado', true)->get();
        // Aquí deberíamos pasar los productos disponibles para consumir, pero lo podemos hacer vía AJAX o pasarlos todos si no son muchos
        $productos = \App\Models\Producto::where('estado', 'activo')->get();

        return view('produccion.ordenes.show', compact('ordenProduccion', 'usuarios', 'procesos', 'productos'));
    }

    public function updateEstado(Request $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:planificada,en_proceso,pausada,completada,cancelada',
        ]);

        if ($validated['estado'] === OrdenProduccion::ESTADO_EN_PROCESO && !$ordenProduccion->fecha_inicio_real) {
            $ordenProduccion->fecha_inicio_real = now();
        } elseif ($validated['estado'] === OrdenProduccion::ESTADO_COMPLETADA && !$ordenProduccion->fecha_fin_real) {
            $ordenProduccion->fecha_fin_real = now();
        }

        $antes = $ordenProduccion->only(['estado', 'fecha_inicio_real', 'fecha_fin_real']);
        $ordenProduccion->update(['estado' => $validated['estado']]);
        AuditoriaService::registrar('produccion.estado_actualizado', $ordenProduccion, $antes, $ordenProduccion->fresh()->only(['estado', 'fecha_inicio_real', 'fecha_fin_real']));

        return back()->with('success', 'Estado de la orden actualizado.');
    }
}

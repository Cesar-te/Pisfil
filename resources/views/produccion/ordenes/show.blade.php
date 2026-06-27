@extends('layouts.app')

@section('title', 'Orden N° ' . $ordenProduccion->numero_orden . ' - PISFIL SIG')
@section('header_title', 'Orden: ' . $ordenProduccion->numero_orden)

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px; justify-content: space-between; align-items: center;">
    <a href="{{ route('ordenes-produccion.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Órdenes
    </a>
    
    <div style="display: flex; gap: 10px;">
        <form action="{{ route('ordenes-produccion.estado', $ordenProduccion) }}" method="POST">
            @csrf
            <select name="estado" onchange="this.form.submit()" style="padding: 8px 15px; border-radius: 20px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none; font-size: 13px;">
                <option value="planificada" {{ $ordenProduccion->estado === 'planificada' ? 'selected' : '' }}>Planificada</option>
                <option value="en_proceso" {{ $ordenProduccion->estado === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                <option value="pausada" {{ $ordenProduccion->estado === 'pausada' ? 'selected' : '' }}>Pausada</option>
                <option value="completada" {{ $ordenProduccion->estado === 'completada' ? 'selected' : '' }}>Completada</option>
            </select>
        </form>
    </div>
</div>

@if($errors->any())
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); color: var(--danger);">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session('success'))
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
        {{ session('success') }}
    </div>
@endif

<div class="kpi-grid stagger-1 mb-8" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
    <div class="kpi-card">
        <span class="kpi-label">Cliente / Proyecto</span>
        <span class="kpi-value" style="font-size: 20px;">{{ $ordenProduccion->cliente }}</span>
        <span class="kpi-delta"><i class="fas fa-calendar"></i> Fin Plan: {{ $ordenProduccion->fecha_fin_planificada->format('d/m/Y') }}</span>
    </div>
    
    @php
        $costoTotalMateriales = $ordenProduccion->consumoMateriales->sum('costo_total');
        $tareasCompletadas = $ordenProduccion->tareas->where('estado', 'completada')->count();
        $totalTareas = $ordenProduccion->tareas->count();
        $progreso = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;
    @endphp
    <div class="kpi-card" style="border-color: rgba(37,99,235,0.3);">
        <span class="kpi-label" style="color: var(--primary);">Progreso de Tareas</span>
        <span class="kpi-value" style="color: var(--primary);">{{ $progreso }}%</span>
        <span class="kpi-delta up"><i class="fas fa-tasks"></i> {{ $tareasCompletadas }} de {{ $totalTareas }} completadas</span>
    </div>

    <div class="kpi-card" style="border-color: rgba(226,114,46,0.3);">
        <span class="kpi-label" style="color: var(--secondary);">Costo de Materiales</span>
        <span class="kpi-value" style="color: var(--secondary);">S/ {{ number_format($costoTotalMateriales, 2) }}</span>
        <span class="kpi-delta warn">
            <i class="fas fa-boxes"></i> Extraído del Kárdex
        </span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px;" class="stagger-2">
    
    <!-- Lado Izquierdo: Tareas de Producción -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        
        <section class="panel table-panel">
            <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Tareas de Trabajo</h2>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Responsable</th>
                            <th>Proceso</th>
                            <th>Avance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenProduccion->tareas as $tarea)
                        <tr>
                            <td>
                                <div style="font-weight: 500;">{{ $tarea->nombre }}</div>
                                <div style="font-size: 11px; color: var(--muted);">{{ $tarea->numero_tarea }}</div>
                            </td>
                            <td>{{ $tarea->usuarioResponsable->name ?? 'N/A' }}</td>
                            <td>{{ $tarea->proceso->nombre ?? '-' }}</td>
                            <td style="min-width: 150px;">
                                <form action="{{ route('tareas-produccion.avance', $tarea) }}" method="POST" style="display: flex; gap: 10px; align-items: center;">
                                    @csrf
                                    <input type="range" name="porcentaje_avance" min="0" max="100" value="{{ $tarea->porcentaje_avance }}" onchange="this.form.submit()" style="flex: 1; accent-color: var(--primary);">
                                    <span style="font-size: 12px; font-family: var(--font-mono); width: 35px; text-align: right;">{{ $tarea->porcentaje_avance }}%</span>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--muted); padding: 20px;">
                                No hay tareas asignadas. Añade tareas desde el formulario de abajo.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Formulario Agregar Tarea -->
        @if($ordenProduccion->estado !== 'completada' && $ordenProduccion->estado !== 'cancelada')
        <section class="panel">
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px;">Asignar Nueva Tarea</h2>
            </div>
            
            <form action="{{ route('tareas-produccion.store', $ordenProduccion) }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Cod. Tarea</label>
                        <input type="text" name="numero_tarea" required placeholder="T-01" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Nombre de Tarea</label>
                        <input type="text" name="nombre" required placeholder="Corte de planchas" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Proceso</label>
                        <select name="proceso_produccion_id" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                            @foreach($procesos as $proc)
                                <option value="{{ $proc->id }}">{{ $proc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Responsable</label>
                        <select name="usuario_responsable_id" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Inicio Planificado</label>
                        <input type="date" name="fecha_inicio_planificada" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Fin Planificado</label>
                        <input type="date" name="fecha_fin_planificada" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>

                <button type="submit" class="pill ok cursor-pointer" style="border: none; justify-content: center;">
                    <i class="fas fa-plus"></i> Asignar Tarea
                </button>
            </form>
        </section>
        @endif
    </div>

    <!-- Lado Derecho: Consumo de Materiales -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        
        <section class="panel table-panel" style="border-color: rgba(226,114,46,0.3);">
            <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="color: var(--secondary);">Kárdex: Materiales Usados</h2>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Cant.</th>
                            <th>Costo Ref.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenProduccion->consumoMateriales as $consumo)
                        <tr>
                            <td>{{ $consumo->producto->nombre ?? 'N/A' }}</td>
                            <td class="mono" style="font-size: 12px;">{{ number_format($consumo->cantidad, 2) }}</td>
                            <td class="mono" style="font-size: 12px;">S/ {{ number_format($consumo->costo_total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--muted); padding: 20px; font-size: 13px;">
                                No se ha consumido ningún material del inventario aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Formulario Extraer Material -->
        @if($ordenProduccion->estado !== 'completada' && $ordenProduccion->estado !== 'cancelada')
        <section class="panel" style="border-color: rgba(226,114,46,0.3);">
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px; color: var(--secondary);">Consumir desde Almacén</h2>
            </div>
            <p style="font-size: 12px; color: var(--muted); margin-bottom: 15px;">
                Esto generará una salida inmediata en el Kárdex calculando el Costo Promedio.
            </p>
            <form action="{{ route('consumos-material.store', $ordenProduccion) }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Producto</label>
                    <select name="producto_id" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                        <option value="">-- Seleccione Material --</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->nombre }} ({{ $prod->codigo }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Cantidad a retirar</label>
                    <input type="number" name="cantidad" step="0.01" min="0.01" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Vincular a Tarea (Opcional)</label>
                    <select name="tarea_produccion_id" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                        <option value="">-- Ninguna --</option>
                        @foreach($ordenProduccion->tareas as $t)
                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="pill pending cursor-pointer" style="border: none; justify-content: center; background: rgba(226,114,46,0.1); color: var(--secondary);">
                    <i class="fas fa-box-open"></i> Extraer del Kárdex
                </button>
            </form>
        </section>
        @endif
        
    </div>
</div>
@endsection

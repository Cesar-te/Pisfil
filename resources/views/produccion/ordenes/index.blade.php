@extends('layouts.app')

@section('title', 'Órdenes de Producción - PISFIL SIG')
@section('header_title', 'Producción y Tareas')

@section('content')
<!-- Acciones -->
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('ordenes-produccion.create') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
        <i class="fas fa-plus"></i> Nueva Orden de Producción
    </a>
</div>

<!-- KPIs -->
@php
    $enProceso = 0;
    $planificadas = 0;
    $completadas = 0;
    foreach($ordenes as $orden) {
        if($orden->estado === 'en_proceso') $enProceso++;
        elseif($orden->estado === 'planificada') $planificadas++;
        elseif($orden->estado === 'completada') $completadas++;
    }
@endphp
<section class="kpi-grid stagger-1 mb-8">
    <div class="kpi-card" style="border-color: rgba(37,99,235,0.3);">
        <span class="kpi-label" style="color: var(--primary);">En Proceso</span>
        <span class="kpi-value" style="color: var(--primary);">{{ $enProceso }}</span>
        <span class="kpi-delta up"><i class="fas fa-hammer"></i> Trabajando actualmente</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Planificadas</span>
        <span class="kpi-value">{{ $planificadas }}</span>
        <span class="kpi-delta"><i class="fas fa-calendar-alt"></i> Esperando inicio</span>
    </div>
    <div class="kpi-card" style="border-color: rgba(79,174,122,0.3);">
        <span class="kpi-label" style="color: var(--success);">Completadas</span>
        <span class="kpi-value" style="color: var(--success);">{{ $completadas }}</span>
        <span class="kpi-delta up"><i class="fas fa-check"></i> Listas para entrega</span>
    </div>
</section>

<!-- Tabla de Órdenes -->
<section class="panel table-panel stagger-2">
    <span class="panel-tag">Gestión</span>
    <div class="panel-head">
        <h2>Listado de Órdenes de Trabajo</h2>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
            {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Nro Orden</th>
                    <th>Cliente / Proyecto</th>
                    <th>Fechas Planificadas</th>
                    <th>Estado</th>
                    <th>Asignado A</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                <tr>
                    <td class="mono" style="font-size: 14px;">{{ $orden->numero_orden }}</td>
                    <td>
                        <div style="font-weight: 500;">{{ $orden->cliente }}</div>
                        <div style="font-size: 12px; color: var(--muted); max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $orden->descripcion_trabajo }}
                        </div>
                    </td>
                    <td style="font-size: 13px; color: var(--muted);">
                        {{ $orden->fecha_inicio_planificada->format('d/m/Y') }} - {{ $orden->fecha_fin_planificada->format('d/m/Y') }}
                    </td>
                    <td>
                        @if($orden->estado === 'planificada')
                            <span class="pill pending">Planificada</span>
                        @elseif($orden->estado === 'en_proceso')
                            <span class="pill ok" style="background: rgba(37,99,235,0.1); color: var(--primary); border-color: rgba(37,99,235,0.2);">En Proceso</span>
                        @elseif($orden->estado === 'pausada')
                            <span class="pill pending" style="background: rgba(226,114,46,0.1); color: var(--secondary); border-color: rgba(226,114,46,0.2);">Pausada</span>
                        @elseif($orden->estado === 'completada')
                            <span class="pill ok">Completada</span>
                        @else
                            <span class="pill danger">Cancelada</span>
                        @endif
                    </td>
                    <td style="font-size: 13px;">
                        @if($orden->usuarioAsignado)
                            <i class="fas fa-user text-muted mr-1"></i> {{ $orden->usuarioAsignado->name }}
                        @else
                            <span style="color: var(--muted);">Sin Asignar</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('ordenes-produccion.show', $orden) }}" class="pill text-decoration-none hover:opacity-80" style="border: 1px solid var(--line); color: var(--text);">
                            <i class="fas fa-eye mr-1"></i> Tareas y Consumos
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--muted); padding: 30px;">
                        No hay registros de órdenes de producción.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $ordenes->links('pagination::tailwind') }}
    </div>
</section>
@endsection

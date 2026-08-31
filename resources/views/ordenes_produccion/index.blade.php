@extends('layouts.app')

@section('title', 'Ordenes de Produccion')
@section('header_title', 'Ordenes de Produccion')

@section('content')
<div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('ordenes-produccion.create') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none">
        <i class="fas fa-plus"></i> Nueva Orden
    </a>
</div>

@if(session('success'))
    <div class="panel mb-4" style="border-color: rgba(79, 174, 122, 0.3); color: var(--success);">
        {{ session('success') }}
    </div>
@endif

<section class="panel table-panel">
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Numero Orden</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                    <tr>
                        <td>{{ $orden->numero_orden }}</td>
                        <td>{{ $orden->cliente }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $orden->estado)) }}</td>
                        <td>{{ $orden->fecha_inicio_planificada->format('d/m/Y') }}</td>
                        <td>{{ $orden->fecha_fin_planificada->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('ordenes-produccion.show', $orden) }}" class="pill text-decoration-none hover:opacity-80" style="border: 1px solid var(--line); color: var(--text);">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 20px;">No hay ordenes registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $ordenes->links() }}
    </div>
</section>
@endsection

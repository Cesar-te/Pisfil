@extends('layouts.app')

@section('title', 'Órdenes de Producción')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Órdenes de Producción</h1>
        <a href="{{ route('ordenes-produccion.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nueva Orden
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="text-left px-4 py-3">Número Orden</th>
                    <th class="text-left px-4 py-3">Cliente</th>
                    <th class="text-left px-4 py-3">Estado</th>
                    <th class="text-left px-4 py-3">Fecha Inicio</th>
                    <th class="text-left px-4 py-3">Fecha Fin</th>
                    <th class="text-center px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">{{ $orden->numero_orden }}</td>
                    <td class="px-4 py-3">{{ $orden->cliente }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-bold
                            @if($orden->estado === 'completada') bg-green-100 text-green-800
                            @elseif($orden->estado === 'en_proceso') bg-blue-100 text-blue-800
                            @elseif($orden->estado === 'pausada') bg-yellow-100 text-yellow-800
                            @elseif($orden->estado === 'cancelada') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $orden->fecha_inicio_planificada->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $orden->fecha_fin_planificada->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('ordenes-produccion.show', $orden) }}" class="text-blue-500 hover:text-blue-700 text-xs">Ver</a>
                        <a href="{{ route('ordenes-produccion.edit', $orden) }}" class="text-green-500 hover:text-green-700 text-xs ml-2">Editar</a>
                        <form method="POST" action="{{ route('ordenes-produccion.destroy', $orden) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs ml-2" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay órdenes registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $ordenes->links() }}
    </div>
</div>
@endsection

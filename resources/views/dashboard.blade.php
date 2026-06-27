@extends('layouts.app')

@section('title', 'Dashboard - PISFIL SIG')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Panel de Control</h1>

    <!-- Estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Órdenes Totales -->
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-2">Órdenes Totales</h3>
            <p class="text-3xl font-bold">{{ $ordenesTotales }}</p>
        </div>

        <!-- Órdenes en Proceso -->
        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-2">En Proceso</h3>
            <p class="text-3xl font-bold">{{ $ordenesEnProceso }}</p>
        </div>

        <!-- Órdenes Completadas -->
        <div class="bg-green-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-2">Completadas</h3>
            <p class="text-3xl font-bold">{{ $ordenesCompletadas }}</p>
        </div>

        <!-- Órdenes por Vencer -->
        <div class="bg-red-500 text-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-semibold mb-2">Por Vencer (7 días)</h3>
            <p class="text-3xl font-bold">{{ $ordenesPorVencer }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Sección de Tareas -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Tareas</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Pendientes:</span>
                    <strong class="text-orange-600">{{ $tareasPendientes }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>En Progreso:</span>
                    <strong class="text-blue-600">{{ $tareasEnProgreso }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Completadas esta semana:</span>
                    <strong class="text-green-600">{{ $tareasCompletadasSemana }}</strong>
                </div>
            </div>
        </div>

        <!-- Sección de Inventario -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Inventario</h2>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Productos Activos:</span>
                    <strong class="text-blue-600">{{ $productosActivos }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Stock Bajo:</span>
                    <strong class="text-red-600">{{ $productosStockBajo }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Valor Total:</span>
                    <strong class="text-green-600">S/ {{ number_format($valorTotalInventario, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos Movimientos -->
    <div class="bg-white rounded-lg shadow p-6 mt-8">
        <h2 class="text-xl font-bold mb-4">Últimos Movimientos de Kardex</h2>
        <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="text-left px-4 py-2">Producto</th>
                    <th class="text-left px-4 py-2">Tipo</th>
                    <th class="text-right px-4 py-2">Cantidad</th>
                    <th class="text-left px-4 py-2">Usuario</th>
                    <th class="text-left px-4 py-2">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimosMovimientos as $movimiento)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $movimiento->producto->nombre }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($movimiento->tipo_movimiento === 'entrada') bg-green-100 text-green-800
                            @elseif($movimiento->tipo_movimiento === 'salida') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($movimiento->tipo_movimiento) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-right">{{ $movimiento->cantidad }}</td>
                    <td class="px-4 py-2">{{ $movimiento->usuario->name }}</td>
                    <td class="px-4 py-2">{{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">No hay movimientos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

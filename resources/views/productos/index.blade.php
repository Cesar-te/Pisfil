@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestión de Productos</h1>
        <a href="{{ route('productos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nuevo Producto
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
                    <th class="text-left px-4 py-3">Código</th>
                    <th class="text-left px-4 py-3">Nombre</th>
                    <th class="text-left px-4 py-3">Categoría</th>
                    <th class="text-right px-4 py-3">Stock Actual</th>
                    <th class="text-right px-4 py-3">Precio Unitario</th>
                    <th class="text-center px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 font-monospace">{{ $producto->codigo }}</td>
                    <td class="px-4 py-3">{{ $producto->nombre }}</td>
                    <td class="px-4 py-3">{{ $producto->categoria->nombre }}</td>
                    <td class="px-4 py-3 text-right">
                        @if($producto->stock_actual <= $producto->stock_minimo)
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-bold">{{ $producto->stock_actual }}</span>
                        @else
                            {{ $producto->stock_actual }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">S/ {{ number_format($producto->precio_unitario, 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('productos.show', $producto) }}" class="text-blue-500 hover:text-blue-700 text-xs">Ver</a>
                        <a href="{{ route('productos.edit', $producto) }}" class="text-green-500 hover:text-green-700 text-xs ml-2">Editar</a>
                        <form method="POST" action="{{ route('productos.destroy', $producto) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs ml-2" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay productos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
@endsection

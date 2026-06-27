@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestión de Usuarios</h1>
        <a href="{{ route('usuarios.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nuevo Usuario
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">DNI / Doc</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">{{ $usuario->name }}</td>
                    <td class="px-4 py-3">{{ $usuario->email }}</td>
                    <td class="px-4 py-3">{{ $usuario->documento_identidad ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">{{ $usuario->rol->nombre ?? 'Sin Rol' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($usuario->estado)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-bold">Activo</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-bold">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="text-blue-500 hover:text-blue-700 text-xs">Editar</a>
                        @if(auth()->id() !== $usuario->id)
                        <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" class="inline-block ml-2">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay usuarios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

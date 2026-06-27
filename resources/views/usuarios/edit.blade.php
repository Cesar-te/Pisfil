@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Editar Usuario: {{ $usuario->name }}</h2>
            <span class="text-gray-500 text-sm font-mono">ID: {{ $usuario->id }}</span>
        </div>
        
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="px-6 py-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block text-gray-700 font-bold mb-2">Nombre Completo</label>
                    <input type="text" name="name" id="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" value="{{ old('name', $usuario->name) }}" required>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" value="{{ old('email', $usuario->email) }}" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="documento_identidad" class="block text-gray-700 font-bold mb-2">Doc. Identidad (DNI)</label>
                    <input type="text" name="documento_identidad" id="documento_identidad" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" value="{{ old('documento_identidad', $usuario->documento_identidad) }}">
                    @error('documento_identidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="telefono" class="block text-gray-700 font-bold mb-2">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" value="{{ old('telefono', $usuario->telefono) }}">
                    @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="rol_id" class="block text-gray-700 font-bold mb-2">Rol</label>
                <select name="rol_id" id="rol_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    <option value="">Seleccione un rol...</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ old('rol_id', $usuario->rol_id) == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
                @error('rol_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="border-t border-gray-200 my-6 pt-4">
                <h3 class="text-md font-semibold text-gray-700 mb-4">Cambiar Contraseña (opcional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="password" class="block text-gray-700 font-bold mb-2">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Dejar en blanco para no cambiar">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-gray-700 font-bold mb-2">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Dejar en blanco para no cambiar">
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="estado" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200" value="1" {{ old('estado', $usuario->estado) ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">Estado Activo</span>
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('usuarios.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

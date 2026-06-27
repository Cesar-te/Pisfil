@extends('layouts.app')

@section('title', 'Nuevo Rol')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">Crear Nuevo Rol</h2>
        </div>
        
        <form action="{{ route('roles.store') }}" method="POST" class="px-6 py-4">
            @csrf
            
            <div class="mb-4">
                <label for="codigo" class="block text-gray-700 font-bold mb-2">Código</label>
                <input type="text" name="codigo" id="codigo" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" value="{{ old('codigo') }}" required>
                @error('codigo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 font-bold mb-2">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" value="{{ old('nombre') }}" required>
                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('descripcion') }}</textarea>
                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6 border-t pt-4">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Permisos de Acceso al Menú</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($modulos as $key => $nombre)
                        <label class="flex items-center p-2 border rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="permisos[]" value="{{ $key }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200" {{ in_array($key, old('permisos', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ $nombre }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="estado" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200" value="1" {{ old('estado', true) ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">Estado Activo</span>
                </label>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('roles.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

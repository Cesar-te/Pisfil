@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">Crear Nuevo Producto</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf

        <div>
            <label for="codigo" class="block text-sm font-semibold mb-2">Código *</label>
            <input type="text" id="codigo" name="codigo" required 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
                value="{{ old('codigo') }}">
        </div>

        <div>
            <label for="nombre" class="block text-sm font-semibold mb-2">Nombre *</label>
            <input type="text" id="nombre" name="nombre" required 
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
                value="{{ old('nombre') }}">
        </div>

        <div>
            <label for="descripcion" class="block text-sm font-semibold mb-2">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500">{{ old('descripcion') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="categoria_id" class="block text-sm font-semibold mb-2">Categoría *</label>
                <select id="categoria_id" name="categoria_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500">
                    <option value="">-- Selecciona una categoría --</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="unidad_medida_id" class="block text-sm font-semibold mb-2">Unidad de Medida *</label>
                <select id="unidad_medida_id" name="unidad_medida_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500">
                    <option value="">-- Selecciona una unidad --</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ old('unidad_medida_id') == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }} ({{ $unidad->simbolo }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="precio_unitario" class="block text-sm font-semibold mb-2">Precio Unitario *</label>
                <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" min="0" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
                    value="{{ old('precio_unitario') }}">
            </div>

            <div>
                <label for="stock_minimo" class="block text-sm font-semibold mb-2">Stock Mínimo *</label>
                <input type="number" id="stock_minimo" name="stock_minimo" min="0" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
                    value="{{ old('stock_minimo') }}">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="stock_maximo" class="block text-sm font-semibold mb-2">Stock Máximo *</label>
                <input type="number" id="stock_maximo" name="stock_maximo" min="0" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500"
                    value="{{ old('stock_maximo') }}">
            </div>

            <div>
                <label for="estado" class="block text-sm font-semibold mb-2">Estado</label>
                <select id="estado" name="estado"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500">
                    <option value="activo" selected>Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <div class="flex gap-2 pt-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Guardar Producto
            </button>
            <a href="{{ route('productos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

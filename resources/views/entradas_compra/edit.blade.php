@extends('layouts.app')

@section('title', 'Editar Compra - PISFIL SIG')
@section('header_title', 'Editar Orden de Compra')

@section('content')
<div class="panel-head mb-4">
    <a href="{{ route('entradas-compra.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Compras
    </a>
</div>

<section class="panel">
    <span class="panel-tag">Documento {{ $entradaCompra->numero_documento }}</span>
    <form action="{{ route('entradas-compra.update', $entradaCompra) }}" method="POST" style="margin-top: 20px; display: flex; flex-direction: column; gap: 18px;">
        @csrf
        @method('PUT')

        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Numero de documento</label>
            <input type="text" name="numero_documento" value="{{ old('numero_documento', $entradaCompra->numero_documento) }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
        </div>

        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Proveedor</label>
            <select name="proveedor_id" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $entradaCompra->proveedor_id) == $proveedor->id ? 'selected' : '' }}>
                        {{ $proveedor->nombre_empresa }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Fecha de emision</label>
            <input type="date" name="fecha_emision" value="{{ old('fecha_emision', optional($entradaCompra->fecha_emision)->format('Y-m-d') ?? $entradaCompra->fecha_emision) }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
        </div>

        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Estado</label>
            <select name="estado" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                @foreach(['pendiente' => 'Pendiente', 'recibida' => 'Recibida', 'validada' => 'Validada', 'rechazada' => 'Rechazada'] as $valor => $texto)
                    <option value="{{ $valor }}" {{ old('estado', $entradaCompra->estado) === $valor ? 'selected' : '' }}>{{ $texto }}</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('entradas-compra.show', $entradaCompra) }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">Cancelar</a>
            <button type="submit" class="pill ok cursor-pointer" style="border: none;">Actualizar</button>
        </div>
    </form>
</section>
@endsection

@extends('layouts.app')

@section('title', 'Nueva Orden de Compra - PISFIL SIG')
@section('header_title', 'Nueva Orden de Compra')

@section('content')
<div class="panel stagger-1" style="max-width: 600px; margin: 0 auto;">
    <span class="panel-tag">Formulario</span>
    <div class="panel-head mb-6">
        <h2>Registrar Documento de Compra</h2>
    </div>

    @if($errors->any())
        <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); color: var(--danger);">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('entradas-compra.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
                <label style="color: var(--muted); font-size: 13px;">Proveedor</label>
                <a href="{{ route('proveedores.create') }}" style="font-size: 11px; color: var(--primary); text-decoration: none;"><i class="fas fa-plus"></i> Nuevo Proveedor</a>
            </div>
            <select name="proveedor_id" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                <option value="">-- Seleccione el proveedor --</option>
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre_empresa }} (RUC: {{ $proveedor->ruc }})</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Número de Documento (Factura/Boleta/Guía)</label>
            <input type="text" name="numero_documento" required placeholder="Ej. F001-000451" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none; font-family: var(--font-mono);">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Fecha de Emisión</label>
            <input type="date" name="fecha_emision" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('entradas-compra.index') }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
                Cancelar
            </a>
            <button type="submit" class="pill ok cursor-pointer" style="border: none;">
                Guardar Documento y Continuar <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </form>
</div>
@endsection

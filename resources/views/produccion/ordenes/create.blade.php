@extends('layouts.app')

@section('title', 'Nueva Orden de Producción - PISFIL SIG')
@section('header_title', 'Nueva Orden de Producción')

@section('content')
<div class="panel stagger-1" style="max-width: 600px; margin: 0 auto;">
    <span class="panel-tag">Formulario</span>
    <div class="panel-head mb-6">
        <h2>Crear Orden de Trabajo</h2>
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

    <form action="{{ route('ordenes-produccion.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Número de Orden (Interno)</label>
            <input type="text" name="numero_orden" required placeholder="Ej. OP-001" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none; font-family: var(--font-mono);">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Cliente / Proyecto</label>
            <input type="text" name="cliente" required placeholder="Ej. Estructuras Metálicas SAC" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Descripción del Trabajo</label>
            <textarea name="descripcion_trabajo" required rows="3" placeholder="Ej. Fabricación de 5 tanques de acero inoxidable..." style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none; resize: vertical;"></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Fecha Inicio Planificada</label>
                <input type="date" name="fecha_inicio_planificada" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Fecha Fin Planificada</label>
                <input type="date" name="fecha_fin_planificada" required value="{{ date('Y-m-d', strtotime('+7 days')) }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Responsable General (Opcional)</label>
            <select name="usuario_asignado_id" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                <option value="">-- Seleccionar encargado general --</option>
                @foreach($usuarios as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->rol->nombre ?? 'Usuario' }})</option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('ordenes-produccion.index') }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
                Cancelar
            </a>
            <button type="submit" class="pill ok cursor-pointer" style="border: none;">
                Crear Orden <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>
    </form>
</div>
@endsection

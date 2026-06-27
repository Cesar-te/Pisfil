@extends('layouts.app')

@section('title', 'Registrar Personal - PISFIL SIG')
@section('header_title', 'Registrar Nuevo Personal')

@section('content')
<div class="panel stagger-1" style="max-width: 700px; margin: 0 auto;">
    <span class="panel-tag">Formulario</span>
    <div class="panel-head mb-6">
        <h2>Alta de Empleado / Usuario</h2>
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

    <form action="{{ route('usuarios.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Nombre Completo</label>
                <input type="text" name="name" required placeholder="Ej. Juan Pérez" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">DNI / Carnet</label>
                <input type="text" name="documento_identidad" required placeholder="Ej. 12345678" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Correo Electrónico (Para Login)</label>
                <input type="email" name="email" required placeholder="ejemplo@pisfil.com" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Teléfono</label>
                <input type="text" name="telefono" placeholder="Ej. 987654321" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Rol / Cargo del Empleado</label>
                <select name="rol_id" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    <option value="">-- Seleccione un rol --</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
                <small style="color: var(--muted); font-size: 11px; display: block; margin-top: 5px;">El rol define a qué pantallas puede acceder si inicia sesión.</small>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Contraseña Temporal (Min 8 carac.)</label>
                <input type="password" name="password" required placeholder="Contraseña segura" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 10px;">
            <a href="{{ route('usuarios.index') }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
                Cancelar
            </a>
            <button type="submit" class="pill ok cursor-pointer" style="border: none;">
                <i class="fas fa-save mr-1"></i> Guardar Empleado
            </button>
        </div>
    </form>
</div>
@endsection

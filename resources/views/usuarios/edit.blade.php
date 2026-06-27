@extends('layouts.app')

@section('title', 'Modificar Empleado - PISFIL SIG')
@section('header_title', 'Modificar: ' . $usuario->name)

@section('content')
<div class="panel stagger-1" style="max-width: 700px; margin: 0 auto;">
    <span class="panel-tag">Formulario</span>
    <div class="panel-head mb-6">
        <h2>Edición de Datos y Accesos</h2>
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

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Nombre Completo</label>
                <input type="text" name="name" value="{{ $usuario->name }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">DNI / Carnet</label>
                <input type="text" name="documento_identidad" value="{{ $usuario->documento_identidad }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Correo Electrónico</label>
                <input type="email" name="email" value="{{ $usuario->email }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Teléfono</label>
                <input type="text" name="telefono" value="{{ $usuario->telefono }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Rol / Cargo</label>
                <select name="rol_id" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Estado (Activo / Baja)</label>
                <select name="estado" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    <option value="1" {{ $usuario->estado ? 'selected' : '' }}>Activo - Puede iniciar sesión</option>
                    <option value="0" {{ !$usuario->estado ? 'selected' : '' }}>Inactivo (Dado de baja)</option>
                </select>
            </div>
        </div>
        
        <div>
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Restablecer Contraseña (Dejar en blanco para no cambiar)</label>
            <input type="password" name="password" placeholder="Nueva contraseña" style="width: 100%; max-width: 50%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 10px;">
            <a href="{{ route('usuarios.index') }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
                Cancelar
            </a>
            <button type="submit" class="pill pending cursor-pointer" style="border: none; background: rgba(37,99,235,0.1); color: var(--primary);">
                <i class="fas fa-save mr-1"></i> Actualizar Cambios
            </button>
        </div>
    </form>
</div>
@endsection

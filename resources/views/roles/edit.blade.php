@extends('layouts.app')

@section('title', 'Editar Permisos - PISFIL SIG')
@section('header_title', 'Permisos de: ' . $rol->nombre)

@section('content')
<div class="panel stagger-1" style="max-width: 800px; margin: 0 auto;">
    <span class="panel-tag">Permisos</span>
    <div class="panel-head mb-6">
        <h2>Configurar Acceso del Rol</h2>
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

    <form action="{{ route('roles.update', $rol) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Nombre del Rol</label>
                <input type="text" name="nombre" value="{{ $rol->nombre }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Descripción (Opcional)</label>
                <input type="text" name="descripcion" value="{{ $rol->descripcion }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Estado del Rol</label>
            <select name="estado" required style="width: 200px; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                <option value="1" {{ $rol->estado ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ !$rol->estado ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        @php
            $permisosActuales = is_string($rol->permisos_json) ? json_decode($rol->permisos_json, true) : (array) $rol->permisos_json;
            $esSuperAdmin = in_array('*', $permisosActuales);
        @endphp

        <div style="margin-bottom: 20px; padding: 15px; border: 1px solid var(--line); border-radius: 8px; background: rgba(37,99,235,0.05);">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="permiso_total" value="1" id="superAdminCheck" {{ $esSuperAdmin ? 'checked' : '' }} style="accent-color: var(--primary); transform: scale(1.2);">
                <span style="font-weight: 500; color: var(--primary);">Otorgar Control Total (Súper Administrador)</span>
            </label>
            <p style="margin-top: 5px; font-size: 12px; color: var(--muted); padding-left: 25px;">
                Si marcas esto, el rol tendrá acceso a TODOS los módulos presentes y futuros sin restricciones.
            </p>
        </div>

        <h3 style="font-size: 14px; margin-bottom: 15px; color: var(--muted); border-bottom: 1px solid var(--line); padding-bottom: 8px;">Permisos Específicos de Módulos</h3>
        
        <div id="permisosEspecificos" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; margin-bottom: 30px; {{ $esSuperAdmin ? 'opacity: 0.5; pointer-events: none;' : '' }}">
            @foreach($permisosDisponibles as $key => $descripcion)
                <label style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid var(--line); border-radius: 8px; cursor: pointer; transition: background 0.2s;">
                    <input type="checkbox" name="permisos[]" value="{{ $key }}" {{ in_array($key, $permisosActuales) ? 'checked' : '' }} style="accent-color: var(--primary);">
                    <span style="font-size: 13px;">{{ $descripcion }}</span>
                </label>
            @endforeach
        </div>

        <script>
            document.getElementById('superAdminCheck').addEventListener('change', function() {
                const contenedor = document.getElementById('permisosEspecificos');
                if (this.checked) {
                    contenedor.style.opacity = '0.5';
                    contenedor.style.pointerEvents = 'none';
                } else {
                    contenedor.style.opacity = '1';
                    contenedor.style.pointerEvents = 'auto';
                }
            });
        </script>

        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('roles.index') }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
                Cancelar
            </a>
            <button type="submit" class="pill ok cursor-pointer" style="border: none;">
                <i class="fas fa-save mr-1"></i> Guardar Permisos
            </button>
        </div>
    </form>
</div>
@endsection

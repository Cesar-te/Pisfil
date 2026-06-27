@extends('layouts.app')

@section('title', 'Crear Nuevo Rol - PISFIL SIG')
@section('header_title', 'Crear Nuevo Rol')

@section('content')
<div class="panel stagger-1" style="max-width: 800px; margin: 0 auto;">
    <span class="panel-tag">Seguridad</span>
    <div class="panel-head mb-6">
        <h2>Crear Rol de Usuario</h2>
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

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Nombre del Rol</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Descripción (Opcional)</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Estado del Rol</label>
            <select name="estado" required style="width: 200px; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                <option value="1" {{ old('estado') == '1' ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <h3 style="margin-bottom: 15px; border-bottom: 1px solid var(--line); padding-bottom: 10px;">Permisos de Acceso</h3>
        
        <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: var(--surface-2); border: 1px solid var(--line); display: flex; align-items: center; gap: 15px;">
            <input type="checkbox" name="permiso_total" value="1" id="superAdminCheck" {{ old('permiso_total') == '1' ? 'checked' : '' }} style="accent-color: var(--primary); transform: scale(1.2);">
            <div>
                <label for="superAdminCheck" style="font-weight: bold; cursor: pointer;">Control Total (Súper Administrador)</label>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: var(--muted);">Este rol tendrá acceso irrestricto a todas las áreas y funciones del sistema, sin importar los permisos individuales marcados abajo.</p>
            </div>
        </div>

        <div class="permisos-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; margin-bottom: 30px;">
            @foreach($permisosDisponibles as $key => $label)
                <label style="display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); cursor: pointer; transition: all 0.2s;">
                    <input type="checkbox" name="permisos[]" value="{{ $key }}" class="permiso-checkbox" {{ is_array(old('permisos')) && in_array($key, old('permisos')) ? 'checked' : '' }} style="accent-color: var(--primary);">
                    <span style="font-size: 13px;">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('roles.index') }}" class="pill" style="border: 1px solid var(--line); color: var(--text); text-decoration: none;">Cancelar</a>
            <button type="submit" class="pill ok cursor-pointer" style="border: none;">
                <i class="fas fa-save"></i> Crear Rol
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const superAdminCheck = document.getElementById('superAdminCheck');
        const checkboxes = document.querySelectorAll('.permiso-checkbox');
        
        function toggleCheckboxes() {
            if(superAdminCheck.checked) {
                checkboxes.forEach(cb => {
                    cb.disabled = true;
                    cb.parentElement.style.opacity = '0.5';
                });
            } else {
                checkboxes.forEach(cb => {
                    cb.disabled = false;
                    cb.parentElement.style.opacity = '1';
                });
            }
        }
        
        superAdminCheck.addEventListener('change', toggleCheckboxes);
        toggleCheckboxes(); // Estado inicial
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Roles y Permisos - PISFIL SIG')
@section('header_title', 'Roles y Permisos del Sistema')

@section('content')
<div class="panel-head mb-4">
    <a href="{{ route('usuarios.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Usuarios
    </a>
</div>

<section class="panel table-panel stagger-1">
    <div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <span class="panel-tag">Seguridad</span>
        <a href="{{ route('roles.create') }}" class="pill ok text-decoration-none" style="border: none;">
            <i class="fas fa-plus"></i> Crear Nuevo Rol
        </a>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
            {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Nombre del Rol</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>N° Empleados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $rol)
                <tr>
                    <td style="font-weight: 500;">
                        <i class="fas fa-shield-alt mr-1" style="color: var(--primary);"></i> {{ $rol->nombre }}
                    </td>
                    <td style="font-size: 13px; color: var(--muted);">{{ $rol->descripcion }}</td>
                    <td>
                        @if($rol->estado)
                            <span class="pill ok">Activo</span>
                        @else
                            <span class="pill danger">Inactivo</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="pill" style="background: rgba(148,163,184,0.1); border: none;">{{ $rol->usuarios_count }}</span>
                    </td>
                    <td>
                        <a href="{{ route('roles.edit', $rol) }}" class="pill text-decoration-none hover:opacity-80" style="border: 1px solid var(--line); color: var(--text);">
                            <i class="fas fa-key mr-1"></i> Permisos
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $roles->links('pagination::tailwind') }}
    </div>
</section>
@endsection

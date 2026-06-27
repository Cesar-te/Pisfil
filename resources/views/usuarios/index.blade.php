@extends('layouts.app')

@section('title', 'Usuarios del Sistema - PISFIL SIG')
@section('header_title', 'Gestión de Personal y Accesos')

@section('content')
<!-- Acciones -->
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('usuarios.create') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
        <i class="fas fa-user-plus"></i> Registrar Nuevo Personal
    </a>
    <a href="{{ route('roles.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-shield-halved"></i> Configurar Roles y Permisos
    </a>
</div>

<!-- Tabla de Usuarios -->
<section class="panel table-panel stagger-1">
    <span class="panel-tag">Administración</span>
    <div class="panel-head">
        <h2>Listado de Personal</h2>
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
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Documento</th>
                    <th>Rol (Cargo)</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $user)
                <tr>
                    <td style="font-weight: 500;">
                        <i class="fas fa-user-circle mr-1 text-muted"></i> {{ $user->name }}
                    </td>
                    <td style="font-size: 13px; color: var(--muted);">{{ $user->email }}</td>
                    <td class="mono" style="font-size: 13px;">{{ $user->documento_identidad }}</td>
                    <td>
                        <span class="pill" style="background: rgba(37,99,235,0.1); color: var(--primary); border-color: rgba(37,99,235,0.2);">
                            {{ $user->rol->nombre ?? 'Sin Rol' }}
                        </span>
                    </td>
                    <td>
                        @if($user->estado)
                            <span class="pill ok">Activo</span>
                        @else
                            <span class="pill danger">Baja</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('usuarios.edit', $user) }}" class="pill text-decoration-none hover:opacity-80" style="border: 1px solid var(--line); color: var(--text);">
                            <i class="fas fa-edit mr-1"></i> Modificar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--muted); padding: 30px;">
                        No se encontraron usuarios.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $usuarios->links('pagination::tailwind') }}
    </div>
</section>
@endsection

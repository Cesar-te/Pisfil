@extends('layouts.app')

@section('title', 'Directorio de Proveedores - PISFIL SIG')
@section('header_title', 'Directorio de Proveedores')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('proveedores.create') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
        <i class="fas fa-plus"></i> Nuevo Proveedor
    </a>
    <a href="{{ route('entradas-compra.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Compras
    </a>
</div>

<div class="panel table-panel stagger-1">
    <div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Proveedores Registrados</h2>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">Cod.</th>
                    <th>RUC</th>
                    <th>Razón Social / Empresa</th>
                    <th>Contacto</th>
                    <th>Celular</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $prov)
                <tr>
                    <td class="font-mono text-muted">{{ $prov->codigo }}</td>
                    <td class="font-mono">{{ $prov->ruc ?? '-' }}</td>
                    <td style="font-weight: 500;">{{ $prov->nombre_empresa }}</td>
                    <td>{{ $prov->nombre_contacto ?? '-' }}</td>
                    <td>{{ $prov->celular ?? '-' }}</td>
                    <td>
                        @if($prov->estado)
                            <span style="color: #10B981; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Activo</span>
                        @else
                            <span style="color: #EF4444; background: rgba(239, 68, 68, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Inactivo</span>
                        @endif
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="{{ route('proveedores.edit', $prov) }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 11px; padding: 4px 8px; display: inline-block;">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--muted); padding: 30px;">
                        <i class="fas fa-address-book" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No hay proveedores registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($proveedores->hasPages())
    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--line);">
        {{ $proveedores->links() }}
    </div>
    @endif
</div>
@endsection

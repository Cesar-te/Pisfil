@extends('layouts.app')

@section('content')
<div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="font-family: var(--font-display); font-size: 24px; color: var(--text);">Catálogo de Productos</h2>
        <p style="color: var(--muted); font-size: 14px; margin-top: 4px;">Gestión de inventario y artículos</p>
    </div>
    <a href="#" class="btn-primary" style="padding: 10px 20px; font-size: 13px; text-decoration: none; display: inline-flex; width: auto; gap: 8px;">
        <i class="fas fa-plus"></i> Nuevo Producto
    </a>
</div>

@if(session('success'))
    <div style="background-color: rgba(79, 174, 122, 0.1); color: var(--success); border: 1px solid rgba(79, 174, 122, 0.2); padding: 12px; border-radius: var(--radius-md); margin-bottom: 24px; font-size: 13px;">
        {{ session('success') }}
    </div>
@endif

<div class="panel stagger-1">
    <span class="panel-tag">Listado de Productos</span>
    
    <div class="table-responsive" style="margin-top: 15px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--line);">
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Código</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Nombre</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Categoría</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Unidad</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase; text-align: right;">Precio</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase; text-align: right;">Stock</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase; text-align: center;">Estado</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px 15px; font-family: var(--font-mono); font-size: 13px; color: var(--primary);">
                            {{ $producto->codigo }}
                        </td>
                        <td style="padding: 12px 15px; font-weight: 500; color: var(--text);">
                            {{ $producto->nombre }}
                        </td>
                        <td style="padding: 12px 15px; color: var(--muted); font-size: 13px;">
                            {{ $producto->categoria ? $producto->categoria->nombre : '-' }}
                        </td>
                        <td style="padding: 12px 15px; color: var(--muted); font-size: 13px;">
                            {{ $producto->unidadMedida ? $producto->unidadMedida->simbolo : '-' }}
                        </td>
                        <td style="padding: 12px 15px; color: var(--text); font-size: 13px; text-align: right;">
                            S/ {{ number_format($producto->precio_unitario, 2) }}
                        </td>
                        <td style="padding: 12px 15px; text-align: right;">
                            @php
                                $stockColor = 'var(--success)';
                                if ($producto->stock_actual <= $producto->stock_minimo) $stockColor = 'var(--danger)';
                                elseif ($producto->stock_actual <= ($producto->stock_minimo * 1.5)) $stockColor = 'var(--accent)';
                            @endphp
                            <span style="color: {{ $stockColor }}; font-weight: bold; font-size: 13px;">
                                {{ $producto->stock_actual }}
                            </span>
                        </td>
                        <td style="padding: 12px 15px; text-align: center;">
                            @if($producto->estado === 'Activo')
                                <span style="background-color: rgba(79, 174, 122, 0.1); color: var(--success); padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">ACTIVO</span>
                            @else
                                <span style="background-color: rgba(217, 83, 79, 0.1); color: var(--danger); padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">INACTIVO</span>
                            @endif
                        </td>
                        <td style="padding: 12px 15px; text-align: right; display: flex; gap: 5px; justify-content: flex-end;">
                            <a href="#" class="pill hover:opacity-80" style="padding: 4px 8px; font-size: 14px; background-color: var(--surface-1); color: var(--text); text-decoration: none;" title="Editar">
                                <i class="fas fa-pen"></i>
                            </a>
                            <button type="button" class="pill hover:opacity-80 cursor-pointer" style="padding: 4px 8px; font-size: 14px; background-color: rgba(255, 71, 87, 0.1); border: 1px solid rgba(255, 71, 87, 0.3); color: var(--danger);" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 30px; text-align: center; color: var(--muted);">
                            No hay productos registrados en el inventario.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($productos->hasPages())
            <div style="margin-top: 20px;">
                {{ $productos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

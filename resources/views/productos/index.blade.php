@extends('layouts.app')

@section('title', 'Catálogo de Productos - PISFIL SIG')
@section('header_title', 'Catálogo de Productos y Almacén')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('productos.create') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
        <i class="fas fa-plus"></i> Registrar Nuevo Producto
    </a>
    <a href="{{ route('inventario.dashboard') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Kárdex
    </a>
</div>

<div class="panel table-panel stagger-1">
    <div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Catálogo de Materiales y Productos</h2>
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
                    <th style="width: 50px;">Cod.</th>
                    <th>Nombre del Producto</th>
                    <th>Unidad</th>
                    <th>Stock Actual</th>
                    <th>Stock Min.</th>
                    <th>Precio Ref.</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $prod)
                <tr>
                    <td class="font-mono text-muted">{{ $prod->codigo }}</td>
                    <td style="font-weight: 500;">
                        {{ $prod->nombre }}
                        @if($prod->descripcion)
                            <div style="font-size: 11px; color: var(--muted); font-weight: normal; margin-top: 2px;">{{ Str::limit($prod->descripcion, 30) }}</div>
                        @endif
                    </td>
                    <td>{{ optional($prod->unidadMedida)->abreviatura ?? 'UN' }}</td>
                    <td class="font-mono">
                        @if($prod->stock_actual <= $prod->stock_minimo)
                            <span style="color: var(--danger); font-weight: bold;">{{ number_format($prod->stock_actual, 2) }}</span>
                            <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 10px; margin-left: 5px;"></i>
                        @else
                            {{ number_format($prod->stock_actual, 2) }}
                        @endif
                    </td>
                    <td class="font-mono text-muted">{{ number_format($prod->stock_minimo, 2) }}</td>
                    <td class="font-mono">S/ {{ number_format($prod->precio_unitario, 2) }}</td>
                    <td>
                        @if($prod->estado === 'activo')
                            <span style="color: #10B981; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Activo</span>
                        @else
                            <span style="color: #EF4444; background: rgba(239, 68, 68, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Inactivo</span>
                        @endif
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <a href="{{ route('productos.edit', $prod) }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 11px; padding: 4px 8px; display: inline-block;">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--muted); padding: 30px;">
                        <i class="fas fa-boxes" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No hay productos registrados en el almacén.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($productos) && $productos->hasPages())
    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--line);">
        {{ $productos->links() }}
    </div>
    @endif
</div>
@endsection

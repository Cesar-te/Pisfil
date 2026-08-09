@extends('layouts.app')

@section('title', 'Reporte de Stock - PISFIL SIG')
@section('header_title', 'Reporte Valorizado de Stock')

@section('content')

<div class="panel-head mb-4" style="display: flex; gap: 10px; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('inventario.dashboard') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
        <a href="{{ route('inventario.create_movimiento') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
            <i class="fas fa-plus"></i> Movimiento
        </a>
    </div>
    <button type="button" class="pill pending cursor-pointer" onclick="window.print()" style="border: none;">
        <i class="fas fa-print"></i> Imprimir
    </button>
</div>

<section class="kpi-grid stagger-1 mb-8">
    <div class="kpi-card">
        <span class="kpi-label">Productos Activos</span>
        <span class="kpi-value">{{ $productos->count() }}</span>
        <span class="kpi-delta up"><i class="fas fa-boxes"></i> Inventario registrado</span>
    </div>
    <div class="kpi-card" style="border-left: 4px solid var(--primary);">
        <span class="kpi-label">Valor Total</span>
        <span class="kpi-value mono">S/ {{ number_format($totalValor, 2) }}</span>
        <span class="kpi-delta"><i class="fas fa-coins"></i> Stock x costo unitario</span>
    </div>
    <div class="kpi-card" style="border-left: 4px solid var(--secondary);">
        <span class="kpi-label">Items en Alerta</span>
        <span class="kpi-value">{{ $productos->filter(fn($p) => $p->stock_actual <= $p->stock_minimo)->count() }}</span>
        <span class="kpi-delta warn"><i class="fas fa-triangle-exclamation"></i> Stock minimo</span>
    </div>
</section>

<section class="panel table-panel stagger-2">
    <span class="panel-tag">Inventario</span>
    <div class="panel-head">
        <h2>Detalle de Stock Valorizado</h2>
        <span class="hint">Metodo: costo unitario actual</span>
    </div>

    <div style="overflow-x: auto; margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Producto</th>
                    <th>Categoria</th>
                    <th>Unidad</th>
                    <th style="text-align: right;">Stock</th>
                    <th style="text-align: right;">Costo Unit.</th>
                    <th style="text-align: right;">Valor Stock</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    @php
                        $valorStock = $producto->stock_actual * $producto->precio_unitario;
                        $stockCritico = $producto->stock_actual <= $producto->stock_minimo;
                    @endphp
                    <tr>
                        <td class="mono">{{ $producto->codigo }}</td>
                        <td style="font-weight: 600;">{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                        <td>{{ $producto->unidadMedida->abreviatura ?? 'N/A' }}</td>
                        <td style="text-align: right;" class="mono">{{ number_format($producto->stock_actual, 2) }}</td>
                        <td style="text-align: right;" class="mono">S/ {{ number_format($producto->precio_unitario, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;" class="mono">S/ {{ number_format($valorStock, 2) }}</td>
                        <td>
                            @if($stockCritico)
                                <span class="pill danger">Stock bajo</span>
                            @else
                                <span class="pill ok">Disponible</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--muted); padding: 30px;">No hay productos activos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align: right;">Valor total</th>
                    <th style="text-align: right;" class="mono">S/ {{ number_format($totalValor, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<style>
    @media print {
        aside, .topbar, .role-context, .panel-head button, .panel-head a { display: none !important; }
        body { overflow: visible; height: auto; background: #fff !important; color: #000 !important; }
        .main-content { overflow: visible; }
        .panel { box-shadow: none !important; border: 1px solid #ddd; }
    }
</style>

@endsection

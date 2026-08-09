@extends('layouts.app')

@section('title', 'Clasificacion ABC - PISFIL SIG')
@section('header_title', 'Clasificacion ABC de Inventario')

@section('content')

<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('inventario.dashboard') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Dashboard
    </a>
    <a href="{{ route('inventario.reporte_stock') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-clipboard-list"></i> Reporte de Stock
    </a>
</div>

<section class="kpi-grid stagger-1 mb-8">
    <div class="kpi-card" style="border-left: 4px solid var(--primary);">
        <span class="kpi-label">Valor Inventario</span>
        <span class="kpi-value mono">S/ {{ number_format($totalValor, 2) }}</span>
        <span class="kpi-delta"><i class="fas fa-chart-pie"></i> Base para clasificacion</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Clase A</span>
        <span class="kpi-value">{{ $productos->where('clasificacion', 'A')->count() }}</span>
        <span class="kpi-delta up"><i class="fas fa-star"></i> Mayor impacto</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Clase B/C</span>
        <span class="kpi-value">{{ $productos->whereIn('clasificacion', ['B', 'C'])->count() }}</span>
        <span class="kpi-delta"><i class="fas fa-layer-group"></i> Control regular</span>
    </div>
</section>

<section class="panel table-panel stagger-2">
    <span class="panel-tag">ABC</span>
    <div class="panel-head">
        <div>
            <h2>Priorizacion por Valor de Stock</h2>
            <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px;">La clasificacion ABC ayuda a identificar los productos con mayor peso economico en inventario.</p>
        </div>
    </div>

    <div style="overflow-x: auto; margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th>Clase</th>
                    <th>Codigo</th>
                    <th>Producto</th>
                    <th style="text-align: right;">Stock</th>
                    <th style="text-align: right;">Costo Unit.</th>
                    <th style="text-align: right;">Valor Stock</th>
                    <th style="text-align: right;">% Valor</th>
                    <th style="text-align: right;">% Acum.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr>
                        <td>
                            @if($producto->clasificacion === 'A')
                                <span class="pill ok">A</span>
                            @elseif($producto->clasificacion === 'B')
                                <span class="pill pending">B</span>
                            @else
                                <span class="pill">C</span>
                            @endif
                        </td>
                        <td class="mono">{{ $producto->codigo }}</td>
                        <td style="font-weight: 600;">{{ $producto->nombre }}</td>
                        <td style="text-align: right;" class="mono">{{ number_format($producto->stock_actual, 2) }}</td>
                        <td style="text-align: right;" class="mono">S/ {{ number_format($producto->precio_unitario, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;" class="mono">S/ {{ number_format($producto->valor_stock, 2) }}</td>
                        <td style="text-align: right;" class="mono">{{ number_format($producto->porcentaje_valor, 2) }}%</td>
                        <td style="text-align: right;" class="mono">{{ number_format($producto->porcentaje_acumulado, 2) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--muted); padding: 30px;">No hay productos activos para clasificar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection

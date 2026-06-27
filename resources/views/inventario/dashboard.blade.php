@extends('layouts.app')

@section('title', 'Kárdex y Logística - PISFIL SIG')
@section('header_title', 'Dashboard de Inventario')

@push('scripts_head')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')

<!-- Acciones Rápidas -->
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('productos.index') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
        <i class="fas fa-boxes"></i> Catálogo de Productos
    </a>
    <a href="{{ route('inventario.create_movimiento') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-plus"></i> Registrar Movimiento Manual
    </a>
    <a href="{{ route('inventario.movimientos_kardex') }}" class="pill pending hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
        <i class="fas fa-list"></i> Ver Historial de Kárdex
    </a>
</div>

<!-- KPIs -->
<section class="kpi-grid stagger-1">
    <div class="kpi-card">
        <span class="kpi-label">Productos Activos</span>
        <span class="kpi-value">{{ $productosActivos }}</span>
        <span class="kpi-delta up"><i class="fas fa-check-circle"></i> Catálogo Actualizado</span>
        <div class="sparkline-box" id="spark1"></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Valor Total (Promedio Ponderado)</span>
        <span class="kpi-value">S/ {{ number_format($valorTotalInventario, 2) }}</span>
        <span class="kpi-delta" style="color: var(--muted);"><i class="fas fa-money-bill"></i> Capital inmovilizado</span>
        <div class="sparkline-box" id="spark2"></div>
    </div>
    <div class="kpi-card" style="{{ $stockBajo > 0 ? 'border-color: rgba(226,114,46,0.3);' : '' }}">
        <span class="kpi-label" style="{{ $stockBajo > 0 ? 'color: var(--secondary);' : '' }}">Alertas de Stock Mínimo</span>
        <span class="kpi-value" style="{{ $stockBajo > 0 ? 'color: var(--secondary);' : 'color: var(--success);' }}">{{ $stockBajo }} Ítems</span>
        @if($stockBajo > 0)
            <span class="kpi-delta warn"><i class="fas fa-triangle-exclamation"></i> Requiere Reabastecimiento</span>
        @else
            <span class="kpi-delta up"><i class="fas fa-check"></i> Stock Saludable</span>
        @endif
        <div class="sparkline-box" id="spark3"></div>
    </div>
</section>

<!-- Tablas de Datos -->
<section class="panel table-panel stagger-2 mt-8">
    <span class="panel-tag">Últimos Movimientos</span>
    <div class="panel-head">
        <h2>Historial Reciente de Kárdex</h2>
        <span class="hint">Método: Promedio Ponderado</span>
    </div>
    
    @if(session('success'))
        <div class="mb-4 p-4 rounded bg-green-100 text-green-700 border border-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo Unit.</th>
                    <th>Saldo Actual</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimosMovimientos as $mov)
                <tr>
                    <td>{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
                    <td class="mono">{{ $mov->producto->nombre }}</td>
                    <td>
                        @if($mov->tipo_movimiento === 'entrada')
                            <span class="pill ok"><i class="fas fa-arrow-down mr-1"></i> Entrada</span>
                        @elseif($mov->tipo_movimiento === 'salida')
                            <span class="pill danger"><i class="fas fa-arrow-up mr-1"></i> Salida</span>
                        @else
                            <span class="pill pending"><i class="fas fa-retweet mr-1"></i> {{ ucfirst($mov->tipo_movimiento) }}</span>
                        @endif
                    </td>
                    <td style="font-family: var(--font-mono); {{ $mov->tipo_movimiento === 'salida' ? 'color: var(--danger);' : 'color: var(--success);' }}">
                        {{ $mov->tipo_movimiento === 'salida' ? '-' : '+' }}{{ $mov->cantidad }}
                    </td>
                    <td>S/ {{ number_format($mov->precio_unitario, 2) }}</td>
                    <td style="font-weight: 600;">{{ $mov->saldo_actual }}</td>
                    <td><span class="hint">{{ $mov->usuario->name ?? 'Sistema' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--muted);">No hay movimientos recientes registrados en el Kárdex.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Configuración de Sparklines (KPIs)
    document.addEventListener('DOMContentLoaded', () => {
        const sparkOptions = {
            chart: { type: 'area', height: 50, sparkline: { enabled: true } },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 100] } },
            tooltip: { fixed: { enabled: false }, x: { show: false }, y: { title: { formatter: function () { return '' } } }, marker: { show: false } }
        };

        if(document.querySelector("#spark1")) new ApexCharts(document.querySelector("#spark1"), { ...sparkOptions, series: [{ data: [30, 40, 35, 50, 49, 60, 70] }], colors: ['#4fae7a'] }).render();
        if(document.querySelector("#spark2")) new ApexCharts(document.querySelector("#spark2"), { ...sparkOptions, series: [{ data: [15, 25, 20, 30, 25, 35, 30] }], colors: ['#8d99a6'] }).render();
        if(document.querySelector("#spark3")) new ApexCharts(document.querySelector("#spark3"), { ...sparkOptions, series: [{ data: [1, 0, 2, 1, 3, 2, {{ $stockBajo }}] }], colors: ['#e2722e'], chart: { type: 'bar', height: 50, sparkline: { enabled: true } } }).render();
    });
</script>
@endpush

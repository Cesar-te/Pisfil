@extends('layouts.app')

@section('title', 'Dashboard - PISFIL SIG v1.0')
@section('header_title', 'Dashboard Operativo')

@push('scripts_head')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<style>
    .kpi-card {
        position: relative;
        overflow: hidden;
        min-height: 172px;
    }
    .kpi-card > span,
    .kpi-card > div:not(.mini-chart) {
        position: relative;
        z-index: 2;
    }
    .mini-chart {
        position: absolute;
        right: 14px;
        bottom: 10px;
        width: 46%;
        height: 72px;
        min-width: 135px;
        opacity: 0.95;
        pointer-events: none;
        z-index: 1;
    }
    .mini-chart.is-donut {
        width: 92px;
        height: 92px;
        right: 18px;
        bottom: 14px;
    }
    @media (max-width: 900px) {
        .mini-chart {
            width: 42%;
            min-width: 120px;
        }
    }
</style>

<section class="kpi-grid stagger-1">
    <a href="{{ route('ventas.index') }}" class="kpi-card" style="text-decoration: none; color: inherit;">
        <span class="kpi-label">Ventas del mes</span>
        <span class="kpi-value mono">S/ {{ number_format($ventasMes, 2) }}</span>
        @if($variacionVentas === null)
            <span class="kpi-delta" style="color: var(--muted);"><i class="fas fa-minus"></i> Sin mes anterior comparable</span>
        @else
            <span class="kpi-delta {{ $variacionVentas >= 0 ? 'up' : 'danger' }}">
                <i class="fas fa-arrow-{{ $variacionVentas >= 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($variacionVentas), 1) }}% vs. mes anterior
            </span>
        @endif
        <div id="miniVentas" class="mini-chart"></div>
    </a>

    <a href="{{ route('ventas.index') }}" class="kpi-card" style="text-decoration: none; color: inherit;">
        <span class="kpi-label">Cuentas por cobrar</span>
        <span class="kpi-value mono">S/ {{ number_format($cuentasPorCobrar, 2) }}</span>
        <span class="kpi-delta" style="color: var(--muted);"><i class="fas fa-clock"></i> {{ $clientesConDeuda }} cliente(s) con saldo</span>
        <div id="miniCobrar" class="mini-chart"></div>
    </a>

    <a href="{{ route('entradas-compra.index') }}" class="kpi-card" style="text-decoration: none; color: inherit;">
        <span class="kpi-label">Cuentas por pagar</span>
        <span class="kpi-value mono">S/ {{ number_format($cuentasPorPagar, 2) }}</span>
        <span class="kpi-delta {{ $cuentasPorPagar > 0 ? 'warn' : 'up' }}">
            <i class="fas fa-file-invoice-dollar"></i> {{ $proveedoresConDeuda }} proveedor(es) pendientes
        </span>
        <div id="miniPagar" class="mini-chart"></div>
    </a>

    <a href="{{ route('inventario.stock_bajo') }}" class="kpi-card" style="text-decoration: none; color: inherit; {{ $productosStockBajo > 0 ? 'border-color: rgba(226,114,46,0.3);' : '' }}">
        <span class="kpi-label" style="{{ $productosStockBajo > 0 ? 'color: var(--secondary);' : '' }}">Alertas de stock</span>
        <span class="kpi-value" style="{{ $productosStockBajo > 0 ? 'color: var(--secondary);' : 'color: var(--success);' }}">{{ $productosStockBajo }} item(s)</span>
        <span class="kpi-delta {{ $productosStockBajo > 0 ? 'warn' : 'up' }}">
            <i class="fas fa-triangle-exclamation"></i> {{ $productosStockBajo > 0 ? 'Requiere revision' : 'Inventario saludable' }}
        </span>
        <div id="miniStock" class="mini-chart is-donut"></div>
    </a>
</section>

<section class="kpi-grid stagger-2">
    <a href="{{ route('inventario.reporte_stock') }}" class="kpi-card" style="text-decoration: none; color: inherit;">
        <span class="kpi-label">Valor inventario</span>
        <span class="kpi-value mono">S/ {{ number_format($valorTotalInventario, 2) }}</span>
        <span class="kpi-delta"><i class="fas fa-boxes-stacked"></i> {{ $productosActivos }} producto(s) activo(s)</span>
        <div id="miniInventario" class="mini-chart"></div>
    </a>

    <a href="{{ route('caja-bancos.dashboard') }}" class="kpi-card" style="text-decoration: none; color: inherit;">
        <span class="kpi-label">Flujo de caja del mes</span>
        <span class="kpi-value mono">S/ {{ number_format($ingresosMes - $egresosMes, 2) }}</span>
        <span class="kpi-delta {{ ($ingresosMes - $egresosMes) >= 0 ? 'up' : 'danger' }}">
            <i class="fas fa-wallet"></i> Ingresos S/ {{ number_format($ingresosMes, 0) }} | Egresos S/ {{ number_format($egresosMes, 0) }}
        </span>
        <div id="miniFlujo" class="mini-chart"></div>
    </a>

    <a href="{{ route('ordenes-produccion.index') }}" class="kpi-card" style="text-decoration: none; color: inherit;">
        <span class="kpi-label">Produccion</span>
        <span class="kpi-value">{{ $ordenesEnProceso }} / {{ $ordenesTotales }}</span>
        <span class="kpi-delta"><i class="fas fa-industry"></i> En proceso / total de ordenes</span>
        <div id="miniProduccion" class="mini-chart is-donut"></div>
    </a>

    <a href="{{ route('contabilidad.libro_diario') }}" class="kpi-card" style="text-decoration: none; color: inherit;">
        <span class="kpi-label">Asientos del mes</span>
        <span class="kpi-value">{{ $asientosMes }}</span>
        <span class="kpi-delta up"><i class="fas fa-book-open"></i> Libro Diario actualizado</span>
        <div id="miniAsientos" class="mini-chart"></div>
    </a>
</section>

<section class="charts-grid stagger-3">
    <article class="panel chart-panel">
        <span class="panel-tag">Flujo</span>
        <div class="panel-head">
            <h2>Ventas vs. Compras</h2>
            <span class="hint">Ultimos 6 meses</span>
        </div>
        <div id="barChart" style="min-height: 280px;"></div>
    </article>

    <article class="panel chart-panel">
        <span class="panel-tag">ABC</span>
        <div class="panel-head">
            <h2>Valorizacion de inventario</h2>
            <a href="{{ route('inventario.clasificacion_abc') }}" class="hint" style="text-decoration: none;">Ver detalle</a>
        </div>
        <div id="donutChart" style="min-height: 260px;"></div>
        <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 12px;">
            @foreach($abcTotales as $clase => $valor)
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span>Clase {{ $clase }}</span>
                    <strong class="mono">S/ {{ number_format($valor, 2) }}</strong>
                </div>
            @endforeach
        </div>
    </article>
</section>

<section class="panel table-panel stagger-4">
    <span class="panel-tag">Kardex</span>
    <div class="panel-head">
        <h2>Movimientos recientes de inventario</h2>
        <a href="{{ route('inventario.movimientos_kardex') }}" class="hint" style="text-decoration: none;">Ver historial</a>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th style="text-align: right;">Cantidad</th>
                    <th style="text-align: right;">Costo Unit.</th>
                    <th style="text-align: right;">Saldo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimosMovimientos as $mov)
                    <tr>
                        <td class="mono">{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
                        <td>{{ $mov->producto->nombre ?? 'Producto no disponible' }}</td>
                        <td>
                            @if($mov->tipo_movimiento === 'entrada')
                                <span class="pill ok">Entrada</span>
                            @elseif($mov->tipo_movimiento === 'salida')
                                <span class="pill danger">Salida</span>
                            @else
                                <span class="pill pending">{{ ucfirst($mov->tipo_movimiento) }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;" class="mono">{{ number_format($mov->cantidad, 2) }}</td>
                        <td style="text-align: right;" class="mono">S/ {{ number_format($mov->precio_unitario, 2) }}</td>
                        <td style="text-align: right; font-weight: bold;" class="mono">{{ number_format($mov->saldo_actual, 2) }}</td>
                        <td>{{ $mov->usuario->name ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--muted); padding: 28px;">Todavia no hay movimientos de inventario registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="panel table-panel stagger-4">
    <span class="panel-tag">Comprobantes</span>
    <div class="panel-head">
        <h2>Ultimos comprobantes</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('ventas.index') }}" class="hint" style="text-decoration: none;">Ventas</a>
            <a href="{{ route('entradas-compra.index') }}" class="hint" style="text-decoration: none;">Compras</a>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th>Tipo</th>
                    <th>Cliente / Proveedor</th>
                    <th style="text-align: right;">Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimosComprobantes as $doc)
                    <tr>
                        <td class="mono">{{ optional($doc['fecha'])->format('d/m/Y') }}</td>
                        <td class="mono">{{ $doc['documento'] ?: 'Sin numero' }}</td>
                        <td>{{ $doc['tipo'] }}</td>
                        <td>{{ $doc['entidad'] }}</td>
                        <td style="text-align: right; font-weight: bold;" class="mono">S/ {{ number_format($doc['monto'], 2) }}</td>
                        <td>
                            @if($doc['estado'] === 'pagado' || $doc['estado'] === 'pagada')
                                <span class="pill ok">Pagado</span>
                            @elseif($doc['estado'] === 'parcial')
                                <span class="pill pending">Parcial</span>
                            @else
                                <span class="pill warn">{{ ucfirst($doc['estado'] ?? 'pendiente') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 28px;">No hay comprobantes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="panel stagger-4">
    <span class="panel-tag">Accesos</span>
    <div class="panel-head">
        <h2>Gestion contable y operativa</h2>
        <span class="hint">Datos reales del sistema</span>
    </div>
    <div class="ledger-grid">
        <a href="{{ route('contabilidad.libro_diario') }}" class="ledger-card" style="text-decoration: none;">
            <i class="fas fa-book-open" style="color: var(--primary); font-size: 20px;"></i>
            <strong>Libro Diario</strong>
            <span>Asientos cronologicos generados</span>
        </a>
        <a href="{{ route('contabilidad.index') }}" class="ledger-card" style="text-decoration: none;">
            <i class="fas fa-calculator" style="color: var(--primary); font-size: 20px;"></i>
            <strong>Resumen Contable</strong>
            <span>Ventas, compras, IGV y caja</span>
        </a>
        <a href="{{ route('reportes.dashboard') }}" class="ledger-card" style="text-decoration: none;">
            <i class="fas fa-chart-column" style="color: var(--primary); font-size: 20px;"></i>
            <strong>Reportes</strong>
            <span>Indicadores gerenciales</span>
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const dashboardData = {
        labels: @json($mesesLabels),
        ventas: @json($ventasChart),
        compras: @json($comprasChart),
        cobrar: @json($cuentasCobrarChart),
        pagar: @json($cuentasPagarChart),
        flujo: @json($flujoCajaChart),
        asientos: @json($asientosChart),
        stockSalud: @json($stockSaludSeries),
        produccion: @json($produccionSeries),
        abcLabels: @json($abcLabels),
        abcSeries: @json($abcSeries),
    };

    const getChartThemeColors = () => {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            text: isDark ? '#8d99a6' : '#64748b',
            grid: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
            primary: isDark ? '#3fa7da' : '#2563eb',
            secondary: isDark ? '#e2722e' : '#ea580c',
            accent: isDark ? '#c9a227' : '#ca8a04',
            success: isDark ? '#4fae7a' : '#16a34a'
        };
    };

    let barChartInstance = null;
    let donutChartInstance = null;
    let miniCharts = [];

    const destroyMiniCharts = () => {
        miniCharts.forEach(chart => chart.destroy());
        miniCharts = [];
    };

    const miniAreaOptions = (selector, data, color, colors) => ({
        series: [{ data }],
        chart: {
            type: 'area',
            height: 72,
            sparkline: { enabled: true },
            animations: { enabled: true, speed: 450 },
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        stroke: { curve: 'smooth', width: 2.5 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 0.2, opacityFrom: 0.35, opacityTo: 0.03, stops: [0, 90, 100] }
        },
        colors: [color],
        tooltip: {
            theme: document.documentElement.getAttribute('data-theme'),
            x: { show: false },
            y: { formatter: value => Number(value).toLocaleString('es-PE', { maximumFractionDigits: 2 }) }
        },
        grid: { show: false }
    });

    const miniBarOptions = (data, color) => ({
        series: [{ data }],
        chart: {
            type: 'bar',
            height: 72,
            sparkline: { enabled: true },
            animations: { enabled: true, speed: 450 },
            toolbar: { show: false }
        },
        colors: [color],
        plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
        tooltip: {
            theme: document.documentElement.getAttribute('data-theme'),
            x: { show: false },
            y: { formatter: value => Number(value).toLocaleString('es-PE') }
        }
    });

    const miniDonutOptions = (data, labels, colorsSet) => ({
        series: data.some(value => Number(value) > 0) ? data : [1],
        labels: data.some(value => Number(value) > 0) ? labels : ['Sin datos'],
        chart: {
            type: 'donut',
            height: 92,
            sparkline: { enabled: true },
            animations: { enabled: true, speed: 450 }
        },
        colors: data.some(value => Number(value) > 0) ? colorsSet : ['rgba(148,163,184,0.25)'],
        stroke: { show: false },
        tooltip: {
            theme: document.documentElement.getAttribute('data-theme'),
            y: { formatter: value => Number(value).toLocaleString('es-PE') }
        },
        plotOptions: { pie: { donut: { size: '68%' } } },
        legend: { show: false },
        dataLabels: { enabled: false }
    });

    function renderMiniCharts() {
        destroyMiniCharts();
        const colors = getChartThemeColors();
        const configs = [
            ['#miniVentas', miniAreaOptions('#miniVentas', dashboardData.ventas, colors.primary, colors)],
            ['#miniCobrar', miniAreaOptions('#miniCobrar', dashboardData.cobrar, colors.accent, colors)],
            ['#miniPagar', miniAreaOptions('#miniPagar', dashboardData.pagar, colors.secondary, colors)],
            ['#miniStock', miniDonutOptions(dashboardData.stockSalud, ['Saludable', 'Bajo'], [colors.success, colors.secondary])],
            ['#miniInventario', miniBarOptions(dashboardData.abcSeries, colors.primary)],
            ['#miniFlujo', miniAreaOptions('#miniFlujo', dashboardData.flujo, colors.success, colors)],
            ['#miniProduccion', miniDonutOptions(dashboardData.produccion, ['En proceso', 'Completadas', 'Otras'], [colors.secondary, colors.success, colors.primary])],
            ['#miniAsientos', miniBarOptions(dashboardData.asientos, colors.success)],
        ];

        configs.forEach(([selector, options]) => {
            const element = document.querySelector(selector);
            if (!element) return;
            const chart = new ApexCharts(element, options);
            miniCharts.push(chart);
            chart.render();
        });
    }

    function renderMainCharts() {
        const colors = getChartThemeColors();
        const theme = document.documentElement.getAttribute('data-theme');

        if (barChartInstance) barChartInstance.destroy();
        barChartInstance = new ApexCharts(document.querySelector("#barChart"), {
            series: [
                { name: 'Ventas', data: dashboardData.ventas },
                { name: 'Compras', data: dashboardData.compras }
            ],
            chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            colors: [colors.primary, colors.secondary],
            plotOptions: { bar: { horizontal: false, columnWidth: '48%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: dashboardData.labels,
                labels: { style: { colors: colors.text } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { labels: { style: { colors: colors.text }, formatter: value => 'S/ ' + Number(value).toLocaleString('es-PE') } },
            grid: { borderColor: colors.grid, strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'left', labels: { colors: colors.text } },
            tooltip: { theme: theme, y: { formatter: value => 'S/ ' + Number(value).toLocaleString('es-PE', { minimumFractionDigits: 2 }) } }
        });
        barChartInstance.render();

        if (donutChartInstance) donutChartInstance.destroy();
        const hasAbcData = dashboardData.abcSeries.some(value => Number(value) > 0);
        donutChartInstance = new ApexCharts(document.querySelector("#donutChart"), {
            series: hasAbcData ? dashboardData.abcSeries : [1],
            labels: hasAbcData ? dashboardData.abcLabels.map(label => 'Clase ' + label) : ['Sin valorizacion'],
            chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif' },
            colors: hasAbcData ? [colors.primary, colors.accent, colors.secondary] : [colors.grid],
            plotOptions: { pie: { donut: { size: '72%', labels: { show: true, name: { color: colors.text }, value: { color: colors.text, fontSize: '18px', fontWeight: 700, formatter: value => hasAbcData ? 'S/ ' + Number(value).toLocaleString('es-PE') : '0' } } } } },
            dataLabels: { enabled: false },
            stroke: { show: false },
            legend: { show: false },
            tooltip: { theme: theme, y: { formatter: value => hasAbcData ? 'S/ ' + Number(value).toLocaleString('es-PE', { minimumFractionDigits: 2 }) : 'Sin datos' } }
        });
        donutChartInstance.render();
        renderMiniCharts();
    }

    document.addEventListener('DOMContentLoaded', renderMainCharts);
    document.addEventListener('themeChanged', renderMainCharts);
</script>
@endpush

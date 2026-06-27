@extends('layouts.app')

@section('title', 'PISFIL SIG v1.0 — Ultimate Dashboard')

@push('scripts_head')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<!-- KPIs -->
<section class="kpi-grid stagger-2">
    <div class="kpi-card">
        <span class="kpi-label">Ventas del mes</span>
        <span class="kpi-value">S/ 48,250.00</span>
        <span class="kpi-delta up"><i class="fas fa-arrow-up"></i> 12% vs. mayo 2026</span>
        <div class="sparkline-box" id="spark1"></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Cuentas por cobrar</span>
        <span class="kpi-value">S/ 19,150.00</span>
        <span class="kpi-delta" style="color: var(--muted);"><i class="fas fa-clock"></i> 3 clientes con deuda</span>
        <div class="sparkline-box" id="spark2"></div>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Cuentas por pagar</span>
        <span class="kpi-value">S/ 7,340.00</span>
        <span class="kpi-delta up" style="color: var(--success);"><i class="fas fa-arrow-down"></i> Deuda controlada</span>
        <div class="sparkline-box" id="spark3"></div>
    </div>
    <div class="kpi-card" style="border-color: rgba(226,114,46,0.3);">
        <span class="kpi-label" style="color: var(--secondary);">Alertas Logística</span>
        <span class="kpi-value" style="color: var(--secondary);">4 Ítems</span>
        <span class="kpi-delta warn"><i class="fas fa-triangle-exclamation"></i> Stock crítico (RF-04)</span>
        <div class="sparkline-box" id="spark4"></div>
    </div>
</section>

<!-- Gráficos Avanzados -->
<section class="charts-grid stagger-3">
    <article class="panel chart-panel">
        <span class="panel-tag">Fig. 02 — Flujo</span>
        <h2>Ventas vs. Compras (Últimos 6 meses)</h2>
        <div id="barChart" style="min-height: 250px;"></div>
    </article>

    <article class="panel chart-panel">
        <span class="panel-tag">Fig. 03 — Pareto</span>
        <h2>Clasificación ABC (Valorización PPP)</h2>
        <div id="donutChart" style="margin-top: 10px;"></div>
        <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed var(--line); display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                <span><i class="fas fa-circle" style="color: var(--primary); font-size: 10px; margin-right: 5px;"></i> Clase A (Planchas/Vigas)</span>
                <strong style="font-family: var(--font-mono);">70%</strong>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px;">
                <span><i class="fas fa-circle" style="color: var(--accent); font-size: 10px; margin-right: 5px;"></i> Clase B (Soldadura)</span>
                <strong style="font-family: var(--font-mono);">20%</strong>
            </div>
        </div>
    </article>
</section>

<!-- Tablas de Datos -->
<section class="panel table-panel stagger-4">
    <span class="panel-tag">Fig. 04 — Kárdex</span>
    <div class="panel-head">
        <h2>Movimientos recientes de inventario</h2>
        <span class="hint">Método: Promedio Ponderado</span>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo Unit.</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="mono">KX-014</td>
                    <td>Plancha acero LAC 1/4"</td>
                    <td>Salida</td>
                    <td style="font-family: var(--font-mono); color: var(--danger);">-6</td>
                    <td>S/ 285.00</td>
                    <td>18</td>
                    <td><span class="pill ok">Stock OK</span></td>
                </tr>
                <tr>
                    <td class="mono">KX-013</td>
                    <td>Tubo estructural 50x50</td>
                    <td>Entrada</td>
                    <td style="font-family: var(--font-mono); color: var(--success);">+120</td>
                    <td>S/ 32.50</td>
                    <td>340</td>
                    <td><span class="pill ok">Stock OK</span></td>
                </tr>
                <tr>
                    <td class="mono">KX-012</td>
                    <td>Electrodo E6011 1/8"</td>
                    <td>Salida</td>
                    <td style="font-family: var(--font-mono); color: var(--danger);">-12</td>
                    <td>S/ 18.90</td>
                    <td>8</td>
                    <td><span class="pill warn">Stock bajo</span></td>
                </tr>
                <tr>
                    <td class="mono">KX-011</td>
                    <td>Perno hexagonal 1/2"</td>
                    <td>Salida</td>
                    <td style="font-family: var(--font-mono); color: var(--danger);">-210</td>
                    <td>S/ 0.45</td>
                    <td>40</td>
                    <td><span class="pill warn">Stock bajo</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="panel table-panel stagger-4">
    <span class="panel-tag">Fig. 05 — Comprobantes</span>
    <div class="panel-head">
        <h2>Últimos Comprobantes Emitidos</h2>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Doc.</th>
                    <th>Tipo</th>
                    <th>Cliente / Proveedor</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="mono"><i class="fas fa-file-invoice" style="margin-right: 5px;"></i> F001-00231</td>
                    <td>Venta</td>
                    <td>Constructora Lambayeque S.A.C.</td>
                    <td style="font-weight: 600;">S/ 12,400.00</td>
                    <td><span class="pill ok">Pagado</span></td>
                </tr>
                <tr>
                    <td class="mono"><i class="fas fa-file-invoice" style="margin-right: 5px;"></i> B001-00089</td>
                    <td>Venta</td>
                    <td>Juan Torres Díaz</td>
                    <td style="font-weight: 600;">S/ 850.00</td>
                    <td><span class="pill pending">Pendiente</span></td>
                </tr>
                <tr>
                    <td class="mono"><i class="fas fa-receipt" style="margin-right: 5px;"></i> F002-00114</td>
                    <td>Compra</td>
                    <td>Aceros Arequipa S.A.</td>
                    <td style="font-weight: 600;">S/ 6,200.00</td>
                    <td><span class="pill ok">Pagado</span></td>
                </tr>
                <tr>
                    <td class="mono"><i class="fas fa-receipt" style="margin-right: 5px;"></i> F002-00113</td>
                    <td>Compra</td>
                    <td>Soldexa Perú S.A.C.</td>
                    <td style="font-weight: 600;">S/ 1,140.00</td>
                    <td><span class="pill danger">Vencido</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<!-- Contabilidad -->
<section class="panel stagger-4">
    <span class="panel-tag">Fig. 06 — PCGE</span>
    <h2>Libros Contables y Estados Financieros</h2>
    <p style="font-size: 13px; color: var(--muted); margin-top: 5px;">Generados automáticamente a partir de los asientos validados bajo el PCGE.</p>

    <div class="ledger-grid">
        <div class="ledger-card">
            <i class="fas fa-book" style="color: var(--primary); font-size: 20px;"></i>
            <strong>Libro Diario</strong>
            <span>Asientos cronológicos del periodo</span>
        </div>
        <div class="ledger-card">
            <i class="fas fa-layer-group" style="color: var(--primary); font-size: 20px;"></i>
            <strong>Libro Mayor</strong>
            <span>Mayorización por cuenta contable</span>
        </div>
        <div class="ledger-card">
            <i class="fas fa-scale-balanced" style="color: var(--primary); font-size: 20px;"></i>
            <strong>Balance Comprobación</strong>
            <span>Validación Debe = Haber</span>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Colores que se inyectarán en ApexCharts según el tema
    const getChartThemeColors = () => {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        return {
            text: isDark ? '#8d99a6' : '#64748b',
            grid: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
            primary: isDark ? '#3fa7da' : '#2563eb',
            secondary: isDark ? '#e2722e' : '#ea580c',
            accent: isDark ? '#c9a227' : '#ca8a04'
        };
    };

    let barChartInstance = null;
    let donutChartInstance = null;

    function renderMainCharts() {
        const colors = getChartThemeColors();
        const theme = document.documentElement.getAttribute('data-theme');

        // Configuración Gráfico de Barras (Ventas vs Compras)
        const barOptions = {
            series: [{ name: 'Ventas', data: [44, 55, 57, 56, 61, 58] }, { name: 'Compras', data: [35, 41, 36, 26, 45, 48] }],
            chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            colors: [colors.primary, colors.secondary],
            plotOptions: { bar: { horizontal: false, columnWidth: '45%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                labels: { style: { colors: colors.text } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: { labels: { style: { colors: colors.text } } },
            grid: { borderColor: colors.grid, strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'left', labels: { colors: colors.text } },
            tooltip: { theme: theme }
        };

        if (barChartInstance) barChartInstance.destroy();
        barChartInstance = new ApexCharts(document.querySelector("#barChart"), barOptions);
        barChartInstance.render();

        // Configuración Gráfico Donut (Pareto ABC)
        const donutOptions = {
            series: [70, 20, 10],
            labels: ['Clase A', 'Clase B', 'Clase C'],
            chart: { type: 'donut', height: 260, fontFamily: 'Inter, sans-serif' },
            colors: [colors.primary, colors.accent, colors.secondary],
            plotOptions: { pie: { donut: { size: '75%', labels: { show: true, name: { color: colors.text }, value: { color: colors.text, fontSize: '24px', fontWeight: 700 } } } } },
            dataLabels: { enabled: false },
            stroke: { show: false },
            legend: { show: false },
            tooltip: { theme: theme }
        };

        if (donutChartInstance) donutChartInstance.destroy();
        donutChartInstance = new ApexCharts(document.querySelector("#donutChart"), donutOptions);
        donutChartInstance.render();
    }

    // Configuración de Sparklines (KPIs)
    const renderSparklines = () => {
        const sparkOptions = {
            chart: { type: 'area', height: 50, sparkline: { enabled: true } },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 100] } },
            tooltip: { fixed: { enabled: false }, x: { show: false }, y: { title: { formatter: function () { return '' } } }, marker: { show: false } }
        };

        new ApexCharts(document.querySelector("#spark1"), { ...sparkOptions, series: [{ data: [30, 40, 35, 50, 49, 60, 70] }], colors: ['#4fae7a'] }).render();
        new ApexCharts(document.querySelector("#spark2"), { ...sparkOptions, series: [{ data: [15, 25, 20, 30, 25, 35, 30] }], colors: ['#8d99a6'] }).render();
        new ApexCharts(document.querySelector("#spark3"), { ...sparkOptions, series: [{ data: [45, 40, 38, 30, 25, 20, 15] }], colors: ['#4fae7a'] }).render();
        new ApexCharts(document.querySelector("#spark4"), { ...sparkOptions, series: [{ data: [1, 0, 2, 1, 3, 2, 4] }], colors: ['#e2722e'], chart: { type: 'bar', height: 50, sparkline: { enabled: true } } }).render();
    };

    // Iniciar gráficos
    document.addEventListener('DOMContentLoaded', () => {
        renderMainCharts();
        renderSparklines();
    });

    // Escuchar el evento themeChanged del Layout
    document.addEventListener('themeChanged', () => {
        renderMainCharts();
    });
</script>
@endpush

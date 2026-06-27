@extends('layouts.app')

@section('title', 'Dashboard Gerencial - PISFIL SIG')
@section('header_title', 'Dashboard Gerencial')

@section('content')
<!-- Importar Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- KPIs Principales -->
<div class="kpi-grid stagger-1 mb-8">
    <div class="kpi-card" style="border-color: rgba(37,99,235,0.3);">
        <span class="kpi-label" style="color: var(--primary);">Ventas del Mes</span>
        <span class="kpi-value mono" style="color: var(--primary);">S/ {{ number_format($ventasMes, 2) }}</span>
        <span class="kpi-delta up"><i class="fas fa-arrow-up"></i> Ingresos operativos</span>
    </div>
    <div class="kpi-card" style="border-color: rgba(79,174,122,0.3);">
        <span class="kpi-label" style="color: var(--success);">Flujo de Caja (Mes)</span>
        <span class="kpi-value mono" style="color: var(--success);">S/ {{ number_format($ingresosMes - $egresosMes, 2) }}</span>
        <span class="kpi-delta {{ ($ingresosMes - $egresosMes) >= 0 ? 'up' : 'down' }}">
            <i class="fas fa-coins"></i> Ingresos S/ {{ number_format($ingresosMes, 0) }} | Egresos S/ {{ number_format($egresosMes, 0) }}
        </span>
    </div>
    <div class="kpi-card" style="border-color: rgba(245,158,11,0.3);">
        <span class="kpi-label" style="color: var(--warning);">Valorización Almacén</span>
        <span class="kpi-value mono" style="color: var(--warning);">S/ {{ number_format($valorizacionAlmacen, 2) }}</span>
        <span class="kpi-delta pending"><i class="fas fa-boxes"></i> Capital inmovilizado</span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;" class="stagger-2 mb-8">
    <!-- Gráfico Financiero -->
    <section class="panel">
        <div class="panel-head mb-4">
            <h2>Flujo Financiero (Últimos 6 Meses)</h2>
        </div>
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="financeChart"></canvas>
        </div>
    </section>

    <!-- Gráfico Producción -->
    <section class="panel">
        <div class="panel-head mb-4">
            <h2>Estado de Producción</h2>
        </div>
        <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center; align-items: center;">
            <canvas id="productionChart"></canvas>
        </div>
    </section>
</div>

<!-- Top Productos -->
<section class="panel table-panel stagger-3">
    <span class="panel-tag">Ventas</span>
    <div class="panel-head mb-4">
        <h2>Top 5 Productos Más Vendidos</h2>
    </div>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Producto</th>
                    <th style="text-align: center;">Unidades Vendidas</th>
                    <th style="text-align: right;">Total Generado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProductos as $index => $prod)
                    <tr>
                        <td style="width: 50px; text-align: center;">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; margin: 0 auto;">
                                {{ $index + 1 }}
                            </div>
                        </td>
                        <td style="font-weight: 500;">{{ $prod->nombre }}</td>
                        <td style="text-align: center; font-family: var(--font-mono);">{{ number_format($prod->total_vendido, 2) }}</td>
                        <td style="text-align: right; font-weight: bold; color: var(--success); font-family: var(--font-mono);">
                            S/ {{ number_format($prod->total_dinero, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--muted); padding: 20px;">
                            Aún no hay datos suficientes de ventas para mostrar el ranking.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<!-- Configuración de Gráficos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colores del sistema
    const rootStyles = getComputedStyle(document.documentElement);
    const colorPrimary = rootStyles.getPropertyValue('--primary').trim();
    const colorSuccess = rootStyles.getPropertyValue('--success').trim();
    const colorDanger = rootStyles.getPropertyValue('--danger').trim();
    const colorWarning = rootStyles.getPropertyValue('--warning').trim();
    const colorText = rootStyles.getPropertyValue('--text').trim();
    const colorLine = rootStyles.getPropertyValue('--line').trim();

    Chart.defaults.color = colorText;
    Chart.defaults.font.family = "'Inter', sans-serif";

    // 1. Gráfico Financiero (Barras)
    const ctxFinance = document.getElementById('financeChart').getContext('2d');
    new Chart(ctxFinance, {
        type: 'bar',
        data: {
            labels: {!! json_encode($mesesLabels) !!},
            datasets: [
                {
                    label: 'Ingresos',
                    data: {!! json_encode($ingresosChart) !!},
                    backgroundColor: colorSuccess,
                    borderRadius: 4,
                },
                {
                    label: 'Egresos',
                    data: {!! json_encode($egresosChart) !!},
                    backgroundColor: colorDanger,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: colorLine }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. Gráfico Producción (Doughnut)
    const ctxProduction = document.getElementById('productionChart').getContext('2d');
    const ordenesData = {!! json_encode(array_values($ordenesStats)) !!};
    
    // Solo dibujar si hay datos
    if (ordenesData.reduce((a, b) => a + b, 0) > 0) {
        new Chart(ctxProduction, {
            type: 'doughnut',
            data: {
                labels: ['Pendientes', 'En Proceso', 'Completadas'],
                datasets: [{
                    data: ordenesData,
                    backgroundColor: [colorDanger, colorWarning, colorSuccess],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    } else {
        const container = document.getElementById('productionChart').parentElement;
        container.innerHTML = '<div style="color: var(--muted); text-align: center;">No hay órdenes de producción registradas.</div>';
    }
});
</script>
@endsection

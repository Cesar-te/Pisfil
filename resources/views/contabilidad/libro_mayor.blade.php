@extends('layouts.app')

@section('title', 'Libro Mayor - PISFIL SIG')
@section('header_title', 'Libro Mayor: ' . ucfirst($fechaInicio->translatedFormat('F Y')))

@section('content')
<section class="panel mb-8" style="background: var(--surface-2); padding: 15px 20px; border-bottom: 2px solid var(--primary);">
    <form action="{{ route('contabilidad.libro_mayor') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Mes</label>
            <select name="mes" style="padding: 8px 15px; border-radius: 5px; background: var(--bg); border: 1px solid var(--line); color: var(--text);">
                @foreach(range(1, 12) as $m)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $mes == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                        {{ ucfirst(\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Anio</label>
            <select name="anio" style="padding: 8px 15px; border-radius: 5px; background: var(--bg); border: 1px solid var(--line); color: var(--text);">
                @foreach(range(date('Y') - 2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="pill ok cursor-pointer" style="border: none;">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        <a href="{{ route('contabilidad.index', ['mes' => $mes, 'anio' => $anio]) }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text); margin-left: auto;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <a href="{{ route('contabilidad.balance_comprobacion', ['mes' => $mes, 'anio' => $anio]) }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
            <i class="fas fa-balance-scale"></i> Balance
        </a>
        <button type="button" class="pill pending cursor-pointer" onclick="window.print()" style="border: none; background: rgba(37,99,235,0.1); color: var(--primary);">
            <i class="fas fa-file-export"></i> Imprimir / PDF
        </button>
    </form>
</section>

<div class="kpi-grid stagger-1 mb-8" style="grid-template-columns: repeat(3, 1fr);">
    <div class="kpi-card">
        <span class="kpi-label">Cuentas con movimiento</span>
        <div class="kpi-value mono">{{ $cuentasMayor->count() }}</div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid var(--success);">
        <span class="kpi-label">Debe acumulado</span>
        <div class="kpi-value mono">S/ {{ number_format($totalDebe, 2) }}</div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid var(--primary);">
        <span class="kpi-label">Haber acumulado</span>
        <div class="kpi-value mono">S/ {{ number_format($totalHaber, 2) }}</div>
    </div>
</div>

<section class="panel table-panel stagger-2">
    <div class="panel-head mb-4">
        <h2>Movimientos por cuenta contable</h2>
        <span class="hint">Agrupado desde Libro Diario</span>
    </div>

    @forelse($cuentasMayor as $grupo)
        <div style="margin-bottom: 24px; border: 1px solid var(--line); border-radius: var(--radius-md); overflow: hidden;">
            <div style="display: flex; justify-content: space-between; gap: 16px; padding: 14px 16px; background: var(--surface-2); border-bottom: 1px solid var(--line); flex-wrap: wrap;">
                <div>
                    <strong class="mono">{{ $grupo['cuenta']->codigo }}</strong>
                    <span>{{ $grupo['cuenta']->descripcion }}</span>
                </div>
                <div class="mono" style="color: {{ $grupo['saldo'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                    Saldo: S/ {{ number_format(abs($grupo['saldo']), 2) }} {{ $grupo['saldo'] >= 0 ? 'Deudor' : 'Acreedor' }}
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="table-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Asiento</th>
                            <th>Glosa</th>
                            <th style="text-align: right;">Debe</th>
                            <th style="text-align: right;">Haber</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grupo['movimientos'] as $detalle)
                            <tr>
                                <td class="mono text-muted">{{ $detalle->asiento->fecha->format('d/m/Y') }}</td>
                                <td class="mono">{{ $detalle->asiento->numero }}</td>
                                <td>{{ $detalle->glosa ?: $detalle->asiento->descripcion }}</td>
                                <td style="text-align: right;" class="mono">
                                    {{ $detalle->tipo_movimiento === 'debe' ? number_format($detalle->monto, 2) : '' }}
                                </td>
                                <td style="text-align: right;" class="mono">
                                    {{ $detalle->tipo_movimiento === 'haber' ? number_format($detalle->monto, 2) : '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align: right;">Totales cuenta</th>
                            <th style="text-align: right;" class="mono">{{ number_format($grupo['totalDebe'], 2) }}</th>
                            <th style="text-align: right;" class="mono">{{ number_format($grupo['totalHaber'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @empty
        <div style="padding: 24px; text-align: center; color: var(--muted);">
            No hay movimientos contables para mayorizar en este periodo.
        </div>
    @endforelse
</section>

<style>
    .table-sm th, .table-sm td {
        padding: 8px 10px;
    }
    @media print {
        body * { visibility: hidden; }
        .stagger-2, .stagger-2 * { visibility: visible; }
        .stagger-2 { position: absolute; left: 0; top: 0; width: 100%; }
        aside, .topbar, .stagger-1, form { display: none !important; }
    }
</style>
@endsection

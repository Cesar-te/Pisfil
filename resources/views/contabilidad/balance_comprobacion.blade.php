@extends('layouts.app')

@section('title', 'Balance de Comprobacion - PISFIL SIG')
@section('header_title', 'Balance de Comprobacion: ' . ucfirst($fechaInicio->translatedFormat('F Y')))

@section('content')
<section class="panel mb-8" style="background: var(--surface-2); padding: 15px 20px; border-bottom: 2px solid var(--primary);">
    <form action="{{ route('contabilidad.balance_comprobacion') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
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
        <a href="{{ route('contabilidad.libro_mayor', ['mes' => $mes, 'anio' => $anio]) }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
            <i class="fas fa-book"></i> Libro Mayor
        </a>
        <a href="{{ route('contabilidad.balance_comprobacion.exportar', ['mes' => $mes, 'anio' => $anio]) }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--success);">
            <i class="fas fa-file-excel"></i> Excel/CSV
        </a>
        <button type="button" class="pill pending cursor-pointer" onclick="window.print()" style="border: none; background: rgba(37,99,235,0.1); color: var(--primary);">
            <i class="fas fa-file-export"></i> Imprimir / PDF
        </button>
    </form>
</section>

<div class="kpi-grid stagger-1 mb-8" style="grid-template-columns: repeat(4, 1fr);">
    <div class="kpi-card">
        <span class="kpi-label">Cuentas evaluadas</span>
        <div class="kpi-value mono">{{ $cuentasBalance->count() }}</div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid var(--success);">
        <span class="kpi-label">Movimiento Debe</span>
        <div class="kpi-value mono">S/ {{ number_format($totalDebe, 2) }}</div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid var(--primary);">
        <span class="kpi-label">Movimiento Haber</span>
        <div class="kpi-value mono">S/ {{ number_format($totalHaber, 2) }}</div>
    </div>
    <div class="kpi-card" style="border-left: 4px solid {{ abs($totalDebe - $totalHaber) < 0.01 ? 'var(--success)' : 'var(--danger)' }};">
        <span class="kpi-label">Cuadre</span>
        <div class="kpi-value mono">S/ {{ number_format(abs($totalDebe - $totalHaber), 2) }}</div>
    </div>
</div>

<section class="panel table-panel stagger-2">
    <div class="panel-head mb-4">
        <h2>Balance por cuenta contable</h2>
 
    </div>
    <div style="overflow-x: auto;">
        <table class="table-sm">
            <thead>
                <tr>
                    <th>Cuenta</th>
                    <th>Descripcion</th>
                    <th style="text-align: right;">Debe</th>
                    <th style="text-align: right;">Haber</th>
                    <th style="text-align: right;">Saldo deudor</th>
                    <th style="text-align: right;">Saldo acreedor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cuentasBalance as $grupo)
                    <tr>
                        <td class="mono">{{ $grupo['cuenta']->codigo }}</td>
                        <td>{{ $grupo['cuenta']->descripcion }}</td>
                        <td style="text-align: right;" class="mono">{{ number_format($grupo['totalDebe'], 2) }}</td>
                        <td style="text-align: right;" class="mono">{{ number_format($grupo['totalHaber'], 2) }}</td>
                        <td style="text-align: right;" class="mono">{{ $grupo['saldoDeudor'] > 0 ? number_format($grupo['saldoDeudor'], 2) : '' }}</td>
                        <td style="text-align: right;" class="mono">{{ $grupo['saldoAcreedor'] > 0 ? number_format($grupo['saldoAcreedor'], 2) : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 20px;">
                            No hay asientos contables para este periodo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align: right;">Totales</th>
                    <th style="text-align: right;" class="mono">{{ number_format($totalDebe, 2) }}</th>
                    <th style="text-align: right;" class="mono">{{ number_format($totalHaber, 2) }}</th>
                    <th style="text-align: right;" class="mono">{{ number_format($totalSaldoDeudor, 2) }}</th>
                    <th style="text-align: right;" class="mono">{{ number_format($totalSaldoAcreedor, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
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

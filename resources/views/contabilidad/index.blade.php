@extends('layouts.app')

@section('title', 'Libros Contables - PISFIL SIG')
@section('header_title', 'Consolidado Contable: ' . ucfirst($fechaInicio->translatedFormat('F Y')))

@section('content')
<!-- Panel de Filtro Superior -->
<section class="panel mb-8" style="background: var(--surface-2); padding: 15px 20px; border-bottom: 2px solid var(--primary);">
    <form action="{{ route('contabilidad.index') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
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
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Año</label>
            <select name="anio" style="padding: 8px 15px; border-radius: 5px; background: var(--bg); border: 1px solid var(--line); color: var(--text);">
                @foreach(range(date('Y') - 2, date('Y')) as $y)
                    <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="pill ok cursor-pointer" style="border: none;">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        <button type="button" class="pill pending cursor-pointer ml-auto" onclick="window.print()" style="border: none; background: rgba(37,99,235,0.1); color: var(--primary);">
            <i class="fas fa-file-export"></i> Imprimir / PDF
        </button>
    </form>
</section>

<!-- KPI Resumen del Mes -->
<div class="kpi-grid stagger-1 mb-8" style="grid-template-columns: repeat(2, 1fr);">
    <!-- IGV Compras (Crédito Fiscal) -->
    <div class="kpi-card" style="border-left: 4px solid var(--danger);">
        <span class="kpi-label" style="font-size: 14px;">Total Compras ({{ $compras->count() }} facturas)</span>
        <div style="font-size: 20px; font-weight: bold; margin-top: 10px; color: var(--text);" class="mono">
            S/ {{ number_format($totalCompras, 2) }}
        </div>
        <div style="display: flex; gap: 15px; margin-top: 10px; font-size: 12px; color: var(--muted);">
            <div>Base Imp: <span class="mono">S/ {{ number_format($baseImponibleCompras, 2) }}</span></div>
            <div>IGV (18%): <span class="mono">S/ {{ number_format($igvCompras, 2) }}</span></div>
        </div>
    </div>
    
    <!-- IGV Ventas (Débito Fiscal) -->
    <div class="kpi-card" style="border-left: 4px solid var(--success);">
        <span class="kpi-label" style="font-size: 14px;">Total Ventas ({{ $ventas->count() }} facturas)</span>
        <div style="font-size: 20px; font-weight: bold; margin-top: 10px; color: var(--text);" class="mono">
            S/ {{ number_format($totalVentas, 2) }}
        </div>
        <div style="display: flex; gap: 15px; margin-top: 10px; font-size: 12px; color: var(--muted);">
            <div>Base Imp: <span class="mono">S/ {{ number_format($baseImponibleVentas, 2) }}</span></div>
            <div>IGV (18%): <span class="mono">S/ {{ number_format($igvVentas, 2) }}</span></div>
        </div>
    </div>
</div>

<div class="stagger-2">
    <!-- REGISTRO DE VENTAS -->
    <section class="panel table-panel" style="margin-bottom: 30px;">
        <div class="panel-head mb-4">
            <h2>Registro de Ventas e Ingresos (Formato Sunat 14.1)</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-sm">
                <thead style="font-size: 11px;">
                    <tr>
                        <th>FECHA EMISIÓN</th>
                        <th>TIPO COMP.</th>
                        <th>SERIE</th>
                        <th>NÚMERO</th>
                        <th>TIPO DOC.</th>
                        <th>N° DOC. (RUC/DNI)</th>
                        <th>APELLIDOS Y NOMBRES / RAZÓN SOCIAL</th>
                        <th style="text-align: right;">BASE IMPONIBLE</th>
                        <th style="text-align: right;">IGV (18%)</th>
                        <th style="text-align: right;">IMPORTE TOTAL</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px;">
                    @forelse($ventas as $venta)
                        <tr>
                            <td class="mono text-muted">{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                            <td>{{ $venta->tipo_comprobante === 'Factura' ? '01' : '03' }}</td>
                            <td class="mono">{{ $venta->serie_comprobante }}</td>
                            <td class="mono">{{ $venta->numero_comprobante }}</td>
                            <td>{{ strlen($venta->cliente->documento_identidad) == 11 ? '6' : '1' }}</td>
                            <td class="mono">{{ $venta->cliente->documento_identidad }}</td>
                            <td>{{ $venta->cliente->nombre }}</td>
                            
                            @php
                                $sub = $venta->total / 1.18;
                                $igv = $venta->total - $sub;
                            @endphp
                            <td style="text-align: right;" class="mono">{{ number_format($sub, 2) }}</td>
                            <td style="text-align: right;" class="mono">{{ number_format($igv, 2) }}</td>
                            <td style="text-align: right; font-weight: bold; color: var(--success);" class="mono">{{ number_format($venta->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--muted); padding: 15px;">No hay ventas registradas en este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- REGISTRO DE COMPRAS -->
    <section class="panel table-panel" style="margin-bottom: 30px;">
        <div class="panel-head mb-4">
            <h2>Registro de Compras (Formato Sunat 8.1)</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-sm">
                <thead style="font-size: 11px;">
                    <tr>
                        <th>FECHA EMISIÓN</th>
                        <th>TIPO COMP.</th>
                        <th>SERIE</th>
                        <th>NÚMERO</th>
                        <th>TIPO DOC.</th>
                        <th>N° DOC. (RUC/DNI)</th>
                        <th>APELLIDOS Y NOMBRES / RAZÓN SOCIAL</th>
                        <th style="text-align: right;">BASE IMPONIBLE</th>
                        <th style="text-align: right;">IGV (18%)</th>
                        <th style="text-align: right;">IMPORTE TOTAL</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px;">
                    @forelse($compras as $compra)
                        <tr>
                            <td class="mono text-muted">{{ $compra->fecha_emision ? $compra->fecha_emision->format('d/m/Y') : '' }}</td>
                            <td>01</td> <!-- Asumimos factura de compra por defecto -->
                            <td class="mono">F001</td> <!-- Placeholder -->
                            <td class="mono">{{ $compra->numero_documento ?? str_pad($compra->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>6</td>
                            <td class="mono">{{ $compra->proveedor->ruc }}</td>
                            <td>{{ $compra->proveedor->razon_social }}</td>
                            
                            @php
                                $total_costo = $compra->detalles->sum('costo_total');
                                $sub = $total_costo / 1.18;
                                $igv = $total_costo - $sub;
                            @endphp
                            <td style="text-align: right;" class="mono">{{ number_format($sub, 2) }}</td>
                            <td style="text-align: right;" class="mono">{{ number_format($igv, 2) }}</td>
                            <td style="text-align: right; font-weight: bold; color: var(--danger);" class="mono">{{ number_format($total_costo, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; color: var(--muted); padding: 15px;">No hay compras registradas en este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- LIBRO CAJA Y BANCOS -->
    <section class="panel table-panel">
        <div class="panel-head mb-4">
            <h2>Libro Caja y Bancos (Detalle de Movimientos del Mes)</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-sm">
                <thead style="font-size: 11px;">
                    <tr>
                        <th>FECHA OPER.</th>
                        <th>CUENTA BANCARIA / CAJA</th>
                        <th>DESCRIPCIÓN DE LA OPERACIÓN</th>
                        <th style="text-align: right;">DEUDOR (Ingreso)</th>
                        <th style="text-align: right;">ACREEDOR (Egreso)</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px;">
                    @forelse($movimientosBancarios as $mov)
                        <tr>
                            <td class="mono text-muted">{{ $mov->fecha_transaccion->format('d/m/Y') }}</td>
                            <td>{{ $mov->cuenta->nombre }} <span style="color: var(--muted);">({{ $mov->cuenta->moneda }})</span></td>
                            <td>
                                {{ $mov->motivo }} 
                                @if($mov->referencia) <span class="mono" style="color: var(--muted);">| Ref: {{ $mov->referencia }}</span> @endif
                            </td>
                            <td style="text-align: right; font-weight: bold; color: var(--success);" class="mono">
                                {{ ($mov->tipo === 'ingreso' || ($mov->tipo === 'transferencia' && str_contains($mov->motivo, 'ENTRANTE'))) ? number_format($mov->monto, 2) : '' }}
                            </td>
                            <td style="text-align: right; font-weight: bold; color: var(--danger);" class="mono">
                                {{ ($mov->tipo === 'egreso' || ($mov->tipo === 'transferencia' && str_contains($mov->motivo, 'SALIENTE'))) ? number_format($mov->monto, 2) : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--muted); padding: 15px;">No hay movimientos bancarios registrados en este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    .table-sm th, .table-sm td {
        padding: 8px 10px;
    }
    @media print {
        body * { visibility: hidden; }
        .stagger-2, .stagger-2 * { visibility: visible; }
        .stagger-2 { position: absolute; left: 0; top: 0; width: 100%; }
        .panel { box-shadow: none !important; border: 1px solid #ddd; page-break-inside: avoid; margin-bottom: 20px;}
        aside, .topbar, .stagger-1, form { display: none !important; }
    }
</style>
@endsection

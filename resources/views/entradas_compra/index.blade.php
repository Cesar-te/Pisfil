@extends('layouts.app')

@section('title', 'Compras y Cuentas por Pagar - PISFIL SIG')
@section('header_title', 'Compras y Cuentas por Pagar')

@section('content')
<!-- Acciones -->
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('entradas-compra.create') }}" class="pill ok hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px;">
        <i class="fas fa-plus"></i> Nueva Orden de Compra
    </a>
    <a href="{{ route('proveedores.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-address-book"></i> Directorio de Proveedores
    </a>
</div>

<!-- KPIs -->
@php
    $totalDeuda = 0;
    $totalPagado = 0;
    $comprasPendientes = 0;
    foreach($entradas as $entrada) {
        $facturaTotal = $entrada->detalles()->sum('costo_total');
        if($entrada->estado_pago !== 'pagado') {
            $totalDeuda += ($facturaTotal - $entrada->monto_pagado);
        }
        $totalPagado += $entrada->monto_pagado;
        
        if($entrada->estado === 'pendiente') {
            $comprasPendientes++;
        }
    }
@endphp
<section class="kpi-grid stagger-1 mb-8">
    <div class="kpi-card" style="border-color: rgba(226,114,46,0.3);">
        <span class="kpi-label" style="color: var(--secondary);">Deuda Total Pendiente (CxP)</span>
        <span class="kpi-value" style="color: var(--secondary);">S/ {{ number_format($totalDeuda, 2) }}</span>
        <span class="kpi-delta warn"><i class="fas fa-money-bill-wave"></i> Obligaciones a proveedores</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Total Pagado a Proveedores</span>
        <span class="kpi-value">S/ {{ number_format($totalPagado, 2) }}</span>
        <span class="kpi-delta up"><i class="fas fa-check-circle"></i> Egresos registrados</span>
    </div>
    <div class="kpi-card">
        <span class="kpi-label">Órdenes de Compra Pendientes</span>
        <span class="kpi-value">{{ $comprasPendientes }}</span>
        <span class="kpi-delta"><i class="fas fa-truck-fast"></i> Esperando recepción en almacén</span>
    </div>
</section>

<!-- Tabla de Compras -->
<section class="panel table-panel stagger-2">
    <span class="panel-tag">Gestión</span>
    <div class="panel-head">
        <h2>Listado de Compras</h2>
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
                    <th>Documento</th>
                    <th>Proveedor</th>
                    <th>Emisión</th>
                    <th>Total Fra.</th>
                    <th>Estado Logístico</th>
                    <th>Estado Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entradas as $entrada)
                @php $facturaTotal = $entrada->detalles()->sum('costo_total'); @endphp
                <tr>
                    <td class="mono" style="font-size: 14px;">{{ $entrada->numero_documento }}</td>
                    <td style="font-weight: 500;">{{ $entrada->proveedor->nombre_empresa ?? 'N/A' }}</td>
                    <td style="font-size: 13px; color: var(--muted);">{{ $entrada->fecha_emision->format('d/m/Y') }}</td>
                    <td style="font-family: var(--font-mono); color: var(--text);">S/ {{ number_format($facturaTotal, 2) }}</td>
                    <td>
                        @if($entrada->estado === 'pendiente')
                            <span class="pill pending">Pendiente</span>
                        @elseif($entrada->estado === 'recibida')
                            <span class="pill ok">Recibida</span>
                        @elseif($entrada->estado === 'validada')
                            <span class="pill ok" style="background: rgba(37,99,235,0.1); color: var(--primary); border-color: rgba(37,99,235,0.2);">Validada (Kárdex)</span>
                        @else
                            <span class="pill danger">Rechazada</span>
                        @endif
                    </td>
                    <td>
                        @if($entrada->estado_pago === 'pagado')
                            <span class="pill ok">Pagado</span>
                        @elseif($entrada->estado_pago === 'parcial')
                            <span class="pill pending">Parcial</span>
                        @else
                            <span class="pill danger">Deuda</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('entradas-compra.show', $entrada) }}" class="pill text-decoration-none hover:opacity-80" style="border: 1px solid var(--line); color: var(--text);">
                            <i class="fas fa-eye mr-1"></i> Detalles / Pagar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--muted); padding: 30px;">
                        No hay registros de compras.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $entradas->links('pagination::tailwind') }}
    </div>
</section>
@endsection

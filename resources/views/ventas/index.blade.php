@extends('layouts.app')

@section('title', 'Ventas y Facturación - PISFIL SIG')
@section('header_title', 'Ventas y Facturación')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 15px;">
    <a href="{{ route('ventas.create') }}" class="pill ok cursor-pointer text-decoration-none" style="border: none;">
        <i class="fas fa-cart-plus mr-1"></i> Nueva Venta (POS)
    </a>
    <a href="{{ route('clientes.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-users mr-1"></i> Directorio de Clientes
    </a>
</div>

@if(session('success'))
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
        {{ session('success') }}
    </div>
@endif

<section class="panel table-panel stagger-1">
    <span class="panel-tag">Historial</span>
    <div class="panel-head mb-4">
        <h2>Registro de Ventas</h2>
    </div>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th>Monto Total</th>
                    <th>Estado / Pago</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $venta)
                    <tr>
                        <td class="mono" style="font-size: 13px; color: var(--muted);">{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                        <td>
                            <div style="font-weight: 500;">{{ strtoupper($venta->tipo_comprobante) }}</div>
                            <div class="mono" style="font-size: 11px; color: var(--muted);">{{ $venta->serie_comprobante }}-{{ $venta->numero_comprobante }}</div>
                        </td>
                        <td>
                            <div style="font-size: 13px;">{{ $venta->cliente->nombre }}</div>
                            <div class="mono" style="font-size: 11px; color: var(--muted);">{{ $venta->cliente->documento_identidad }}</div>
                        </td>
                        <td style="font-weight: bold; font-family: var(--font-mono); color: var(--success); font-size: 14px;">
                            {{ $venta->moneda === 'PEN' ? 'S/' : '$' }} {{ number_format($venta->total, 2) }}
                        </td>
                        <td>
                            @if($venta->estado === 'pagada')
                                <span class="pill ok" style="padding: 2px 8px; font-size: 11px;">PAGADA</span>
                                <div style="font-size: 10px; color: var(--muted); margin-top: 4px;">En: {{ $venta->cuentaFinanciera?->nombre ?? 'N/A' }}</div>
                            @elseif($venta->estado === 'borrador')
                                <span class="pill pending" style="padding: 2px 8px; font-size: 11px;">BORRADOR</span>
                            @else
                                <span class="pill danger" style="padding: 2px 8px; font-size: 11px;">ANULADA</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('ventas.show', $venta) }}" class="icon-btn hover:text-primary" title="Ver Comprobante">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 20px;">
                            No hay ventas registradas. Haz clic en "Nueva Venta (POS)" para comenzar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px;">
        {{ $ventas->links('pagination::tailwind') }}
    </div>
</section>
@endsection

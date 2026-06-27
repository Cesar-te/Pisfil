@extends('layouts.app')

@section('title', 'Comprobante de Venta - PISFIL SIG')
@section('header_title', 'Comprobante de Venta')

@section('content')
<div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('ventas.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Ventas
    </a>
    <button onclick="window.print()" class="pill ok cursor-pointer text-decoration-none" style="border: none;">
        <i class="fas fa-print"></i> Imprimir Comprobante
    </button>
</div>

@if(session('success'))
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
        {{ session('success') }}
    </div>
@endif

<!-- Documento A4-like -->
<div class="panel" style="max-width: 800px; margin: 0 auto; background: white; color: black; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    
    <!-- Cabecera Factura -->
    <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px;">
        <div>
            <h1 style="font-size: 24px; margin: 0; color: #111;">PISFIL EMSAC</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; color: #555;">Servicios Metalmecánicos Integrales</p>
            <p style="margin: 2px 0 0 0; font-size: 12px; color: #777;">RUC: 20000000001</p>
        </div>
        <div style="text-align: right; border: 1px solid #333; padding: 10px 20px; border-radius: 5px;">
            <div style="font-size: 18px; font-weight: bold;">{{ strtoupper($venta->tipo_comprobante) }}</div>
            <div style="font-size: 16px; margin-top: 5px;" class="mono">{{ $venta->serie_comprobante }} - {{ str_pad($venta->numero_comprobante, 8, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    <!-- Datos Cliente -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; font-size: 14px;">
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 100px; color: #666; padding: 3px 0;"><strong>Cliente:</strong></td>
                    <td style="padding: 3px 0;">{{ $venta->cliente->nombre }}</td>
                </tr>
                <tr>
                    <td style="color: #666; padding: 3px 0;"><strong>RUC/DNI:</strong></td>
                    <td style="padding: 3px 0;">{{ $venta->cliente->documento_identidad }}</td>
                </tr>
                <tr>
                    <td style="color: #666; padding: 3px 0;"><strong>Dirección:</strong></td>
                    <td style="padding: 3px 0;">{{ $venta->cliente->direccion ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="color: #666; padding: 3px 0;"><strong>Fecha:</strong></td>
                    <td style="padding: 3px 0;">{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="color: #666; padding: 3px 0;"><strong>Moneda:</strong></td>
                    <td style="padding: 3px 0;">{{ $venta->moneda === 'PEN' ? 'Soles (PEN)' : 'Dólares (USD)' }}</td>
                </tr>
                <tr>
                    <td style="color: #666; padding: 3px 0;"><strong>Estado:</strong></td>
                    <td style="padding: 3px 0; font-weight: bold; color: {{ $venta->estado === 'pagada' ? '#166534' : '#991b1b' }};">
                        {{ strtoupper($venta->estado) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Detalle -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 14px;">
        <thead>
            <tr style="background: #eee;">
                <th style="padding: 10px; text-align: center; border: 1px solid #ddd; width: 60px;">Cant.</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Descripción del Producto / Servicio</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd; width: 120px;">P. Unit.</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #ddd; width: 120px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
                <tr>
                    <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">{{ number_format($detalle->cantidad, 2) }}</td>
                    <td style="padding: 10px; text-align: left; border: 1px solid #ddd;">
                        [{{ $detalle->producto->codigo }}] {{ $detalle->producto->nombre }}
                    </td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;" class="mono">{{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #ddd;" class="mono">{{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <div style="display: flex; justify-content: flex-end;">
        <table style="width: 300px; border-collapse: collapse; font-size: 14px;">
            <tr>
                <td style="padding: 8px; text-align: right; color: #666;"><strong>OP. GRAVADA:</strong></td>
                <td style="padding: 8px; text-align: right; border: 1px solid #ddd;" class="mono">
                    {{ $venta->moneda === 'PEN' ? 'S/' : '$' }} {{ number_format($venta->total / 1.18, 2) }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px; text-align: right; color: #666;"><strong>IGV (18%):</strong></td>
                <td style="padding: 8px; text-align: right; border: 1px solid #ddd;" class="mono">
                    {{ $venta->moneda === 'PEN' ? 'S/' : '$' }} {{ number_format($venta->total - ($venta->total / 1.18), 2) }}
                </td>
            </tr>
            <tr style="background: #f9f9f9;">
                <td style="padding: 12px 8px; text-align: right; color: #111; font-size: 18px;"><strong>TOTAL:</strong></td>
                <td style="padding: 12px 8px; text-align: right; border: 1px solid #333; font-weight: bold; font-size: 18px;" class="mono">
                    {{ $venta->moneda === 'PEN' ? 'S/' : '$' }} {{ number_format($venta->total, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Info Pago -->
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666;">
        <p style="margin: 0 0 5px 0;"><strong>Condición de Pago:</strong> AL CONTADO</p>
        <p style="margin: 0 0 5px 0;"><strong>Abonado en:</strong> {{ $venta->cuentaFinanciera->nombre ?? 'N/A' }}</p>
        <p style="margin: 0;"><strong>Emitido por:</strong> {{ $venta->usuarioRegistra->name ?? 'Sistema' }}</p>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .panel, .panel * {
            visibility: visible;
        }
        .panel {
            position: absolute;
            left: 0;
            top: 0;
            box-shadow: none !important;
            width: 100%;
        }
        .pill, .icon-btn, .topbar, aside {
            display: none !important;
        }
    }
</style>
@endsection

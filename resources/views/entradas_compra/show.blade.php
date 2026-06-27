@extends('layouts.app')

@section('title', 'Detalle de Compra - PISFIL SIG')
@section('header_title', 'Orden de Compra N° ' . $entradaCompra->numero_documento)

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('entradas-compra.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Compras
    </a>
</div>

@if($errors->any())
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); color: var(--danger);">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session('success'))
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
        {{ session('success') }}
    </div>
@endif

<div class="kpi-grid stagger-1 mb-8" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
    <div class="kpi-card">
        <span class="kpi-label">Proveedor</span>
        <span class="kpi-value" style="font-size: 20px;">{{ $entradaCompra->proveedor->nombre_empresa ?? 'N/A' }}</span>
        <span class="kpi-delta"><i class="fas fa-id-card"></i> RUC: {{ $entradaCompra->proveedor->ruc ?? '-' }}</span>
    </div>
    
    @php
        $facturaTotal = $entradaCompra->detalles()->sum('costo_total');
        $deuda = $facturaTotal - $entradaCompra->monto_pagado;
    @endphp
    <div class="kpi-card" style="border-color: rgba(37,99,235,0.3);">
        <span class="kpi-label" style="color: var(--primary);">Total Facturado</span>
        <span class="kpi-value" style="color: var(--primary);">S/ {{ number_format($facturaTotal, 2) }}</span>
        <span class="kpi-delta up"><i class="fas fa-coins"></i> {{ $entradaCompra->detalles()->count() }} ítems en esta orden</span>
    </div>

    <div class="kpi-card" style="border-color: {{ $deuda > 0 ? 'rgba(226,114,46,0.3)' : 'rgba(79,174,122,0.3)' }};">
        <span class="kpi-label" style="color: {{ $deuda > 0 ? 'var(--secondary)' : 'var(--success)' }};">Deuda Pendiente</span>
        <span class="kpi-value" style="color: {{ $deuda > 0 ? 'var(--secondary)' : 'var(--success)' }};">S/ {{ number_format($deuda, 2) }}</span>
        <span class="kpi-delta {{ $deuda > 0 ? 'warn' : 'up' }}">
            <i class="fas fa-info-circle"></i> Estado Pago: {{ ucfirst($entradaCompra->estado_pago) }}
        </span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;" class="stagger-2">
    <!-- Detalles de la Factura -->
    <section class="panel table-panel">
        <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Materiales de la Orden</h2>
            @if($entradaCompra->estado === 'pendiente' || $entradaCompra->estado === 'recibida')
                <span class="pill pending">Agregando detalles...</span>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="detallesTableBody">
                    @forelse($entradaCompra->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                        <td class="mono">{{ number_format($detalle->cantidad_solicitada, 2) }}</td>
                        <td class="mono">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="mono">S/ {{ number_format($detalle->costo_total, 2) }}</td>
                    </tr>
                    @empty
                    <tr id="noDetallesRow">
                        <td colspan="4" style="text-align: center; color: var(--muted); padding: 20px;">
                            No hay productos en esta orden. Usa el panel derecho para agregarlos.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Acciones Laterales -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        @if($entradaCompra->estado === 'pendiente' || $entradaCompra->estado === 'recibida')
        <!-- Agregar Producto -->
        <section class="panel">
            <span class="panel-tag">Añadir Producto</span>
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px;">Agregar a la factura</h2>
            </div>
            
            <form id="formAgregarDetalle" onsubmit="agregarDetalle(event)" style="display: flex; flex-direction: column; gap: 15px;">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Producto</label>
                    <select id="producto_id" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                        <!-- TODO: Cargar productos -->
                        @php $productos = App\Models\Producto::where('estado', true)->get(); @endphp
                        @foreach($productos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->codigo }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Cantidad</label>
                        <input type="number" id="cantidad_solicitada" step="0.01" min="0.01" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Precio Unit.</label>
                        <input type="number" id="precio_unitario" step="0.01" min="0" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>
                <button type="submit" class="pill ok cursor-pointer" style="border: none; justify-content: center;">
                    <i class="fas fa-plus"></i> Añadir
                </button>
            </form>
        </section>
        
        <!-- Validar Compra -->
        <section class="panel" style="border-color: rgba(37,99,235,0.3);">
            <span class="panel-tag" style="background: rgba(37,99,235,0.1); color: var(--primary);">Recepción</span>
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px;">Validar e Ingresar a Kárdex</h2>
            </div>
            <p style="font-size: 12px; color: var(--muted); margin-bottom: 15px;">
                Al validar esta orden, todos los materiales ingresarán automáticamente al almacén y afectarán el Costo Promedio.
            </p>
            <form action="{{ route('entradas-compra.cambiar-estado', $entradaCompra) }}" method="POST">
                @csrf
                <input type="hidden" name="estado" value="validada">
                <button type="submit" class="pill" style="width: 100%; justify-content: center; background: rgba(37,99,235,0.1); color: var(--primary); border: 1px solid rgba(37,99,235,0.2);">
                    <i class="fas fa-check-double"></i> Aprobar Recepción Logística
                </button>
            </form>
        </section>
        @endif

        @if($entradaCompra->estado === 'validada' && $entradaCompra->estado_pago !== 'pagado')
        <!-- Pago a Proveedor -->
        <section class="panel" style="border-color: rgba(226,114,46,0.3);">
            <span class="panel-tag" style="background: rgba(226,114,46,0.1); color: var(--secondary);">Finanzas</span>
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px;">Registrar Pago a Proveedor</h2>
            </div>
            
            <form action="{{ route('entradas-compra.registrar-pago', $entradaCompra) }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Monto a Pagar (Deuda: S/ {{ number_format($deuda, 2) }})</label>
                    <input type="number" name="monto" step="0.01" max="{{ $deuda }}" min="0.01" value="{{ $deuda }}" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>
                <button type="submit" class="pill warn cursor-pointer" style="border: none; justify-content: center;">
                    <i class="fas fa-money-bill-wave"></i> Procesar Pago
                </button>
            </form>
        </section>
        @endif
        
    </div>
</div>

<script>
    async function agregarDetalle(e) {
        e.preventDefault();
        
        const productoId = document.getElementById('producto_id').value;
        const cantidad = document.getElementById('cantidad_solicitada').value;
        const precio = document.getElementById('precio_unitario').value;
        const token = document.querySelector('input[name="_token"]').value;
        
        try {
            const response = await fetch("{{ route('entradas-compra.agregar-detalle', $entradaCompra) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    producto_id: productoId,
                    cantidad_solicitada: cantidad,
                    precio_unitario: precio
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload(); // Recargar la página para actualizar todos los totales (forma más sencilla por ahora)
            } else {
                alert('Error al agregar el detalle');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ocurrió un error en la solicitud.');
        }
    }
</script>
@endsection

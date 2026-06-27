@extends('layouts.app')

@section('title', 'Punto de Venta - PISFIL SIG')
@section('header_title', 'Nuevo Comprobante de Venta')

@section('content')
<form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
    @csrf

    @if($errors->any())
        <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); color: var(--danger);">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px;">
        
        <!-- Izquierda: Formulario Principal -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Datos de Cabecera -->
            <section class="panel">
                <span class="panel-tag">Cabecera</span>
                <div class="panel-head mb-4">
                    <h2>Datos del Comprobante</h2>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Cliente <a href="{{ route('clientes.index') }}" target="_blank" style="color: var(--primary); margin-left: 10px;">(Nuevo)</a></label>
                        <select name="cliente_id" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                            <option value="">-- Seleccione un cliente --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->documento_identidad }} - {{ $cliente->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Fecha de Emisión</label>
                        <input type="date" name="fecha_venta" value="{{ old('fecha_venta', date('Y-m-d')) }}" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Tipo Comprobante</label>
                        <select name="tipo_comprobante" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                            <option value="Factura">Factura</option>
                            <option value="Boleta">Boleta</option>
                            <option value="Ticket">Ticket de Venta</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Serie</label>
                        <input type="text" name="serie_comprobante" value="F001" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Correlativo (Número)</label>
                        <input type="text" name="numero_comprobante" placeholder="0000001" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>
            </section>

            <!-- Carrito de Productos -->
            <section class="panel table-panel">
                <div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 20px 0 20px;">
                    <h2>Productos a Vender</h2>
                    <button type="button" class="pill pending cursor-pointer" onclick="agregarFila()" style="border: none; background: rgba(37,99,235,0.1); color: var(--primary);">
                        <i class="fas fa-plus"></i> Añadir Ítem
                    </button>
                </div>
                
                <div style="overflow-x: auto;">
                    <table id="tablaProductos">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="filasProductos">
                            <!-- Filas dinámicas -->
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Derecha: Resumen y Pago -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <section class="panel" style="position: sticky; top: 20px;">
                <div class="panel-head mb-4">
                    <h2>Resumen de Venta</h2>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                    <span style="color: var(--muted);">Subtotal:</span>
                    <span id="txtSubtotal" class="mono" style="font-weight: bold;">0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
                    <span style="color: var(--muted);">IGV (18%):</span>
                    <span id="txtIGV" class="mono" style="font-weight: bold;">0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--line); padding-top: 15px; font-size: 18px;">
                    <span>TOTAL:</span>
                    <span id="txtTotal" class="mono" style="font-weight: bold; color: var(--success); font-size: 24px;">0.00</span>
                </div>

                <div style="margin-top: 25px;">
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Moneda</label>
                    <select name="moneda" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                        <option value="PEN">Soles (PEN)</option>
                        <option value="USD">Dólares (USD)</option>
                    </select>
                </div>

                <div style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px; color: var(--success);">Cobrar en (Destino del Dinero)</label>
                    <select name="cuenta_financiera_id" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--success); color: var(--text);">
                        <option value="">-- Seleccionar Cuenta / Caja --</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->nombre }} ({{ $cuenta->moneda }}) - Saldo: {{ number_format($cuenta->saldo_actual, 2) }}</option>
                        @endforeach
                    </select>
                    <small style="color: var(--muted); font-size: 11px; display: block; margin-top: 5px;">* El total se sumará automáticamente a esta cuenta al procesar la venta.</small>
                </div>

                <button type="submit" class="pill ok cursor-pointer" style="width: 100%; justify-content: center; padding: 15px; font-size: 16px; margin-top: 20px; border: none; box-shadow: 0 4px 15px rgba(79, 174, 122, 0.3);">
                    <i class="fas fa-check-circle mr-2"></i> Procesar Venta y Cobrar
                </button>
            </section>
        </div>

    </div>
</form>

<!-- Template para JavaScript -->
<template id="filaProductoTemplate">
    <tr>
        <td>
            <select name="productos[]" required class="select-producto" onchange="calcularFila(this)" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                <option value="">Seleccione Producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}" data-precio="{{ $producto->costo_estimado }}">
                        [{{ $producto->codigo }}] {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="cantidades[]" step="0.01" min="0.01" value="1" required oninput="calcularFila(this)" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text); font-family: var(--font-mono);">
        </td>
        <td>
            <input type="number" name="precios[]" step="0.01" min="0" required oninput="calcularFila(this)" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text); font-family: var(--font-mono);">
        </td>
        <td class="mono txt-subtotal" style="font-weight: bold; font-size: 14px; text-align: right;">
            0.00
        </td>
        <td style="text-align: center;">
            <button type="button" class="icon-btn hover:text-danger" onclick="eliminarFila(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    function agregarFila() {
        const template = document.getElementById('filaProductoTemplate');
        const tbody = document.getElementById('filasProductos');
        const clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
    }

    function eliminarFila(btn) {
        btn.closest('tr').remove();
        calcularTotal();
    }

    function calcularFila(element) {
        const tr = element.closest('tr');
        const selectProd = tr.querySelector('.select-producto');
        const inputCant = tr.querySelector('input[name="cantidades[]"]');
        const inputPrecio = tr.querySelector('input[name="precios[]"]');
        const tdSubtotal = tr.querySelector('.txt-subtotal');

        // Si cambia el producto, cargar el precio sugerido
        if (element.classList.contains('select-producto') && selectProd.selectedIndex > 0) {
            const precioSugerido = selectProd.options[selectProd.selectedIndex].getAttribute('data-precio');
            if (precioSugerido > 0) {
                inputPrecio.value = parseFloat(precioSugerido).toFixed(2);
            }
        }

        const cant = parseFloat(inputCant.value) || 0;
        const precio = parseFloat(inputPrecio.value) || 0;
        const subtotal = cant * precio;
        
        tdSubtotal.textContent = subtotal.toFixed(2);
        calcularTotal();
    }

    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.txt-subtotal').forEach(td => {
            total += parseFloat(td.textContent) || 0;
        });

        // Asumiendo que el precio ya incluye IGV para hacerlo simple en el POS
        const subtotal = total / 1.18;
        const igv = total - subtotal;

        document.getElementById('txtSubtotal').textContent = subtotal.toFixed(2);
        document.getElementById('txtIGV').textContent = igv.toFixed(2);
        document.getElementById('txtTotal').textContent = total.toFixed(2);
    }

    // Agregar una fila vacía al cargar
    window.onload = function() {
        agregarFila();
    };
</script>
@endsection

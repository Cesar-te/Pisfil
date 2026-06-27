@extends('layouts.app')

@section('title', 'Nuevo Movimiento - PISFIL SIG')
@section('header_title', 'Registrar Movimiento en Kárdex')

@section('content')
<section class="panel stagger-1" style="max-width: 800px; margin: 0 auto;">
    <span class="panel-tag">Formulario</span>
    <h2 style="font-family: var(--font-display); margin-bottom: 24px;">Ingreso Manual / Ajuste</h2>

    @if($errors->any())
        <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(217, 83, 79, 0.1); border: 1px solid rgba(217, 83, 79, 0.3); color: var(--danger);">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inventario.store_movimiento') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf
        
        <div>
            <label class="kpi-label" style="display: block;">Producto *</label>
            <select name="producto_id" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
                <option value="">Seleccione un producto</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                        [{{ $producto->codigo }}] {{ $producto->nombre }} (Stock: {{ $producto->stock_actual }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label class="kpi-label" style="display: block;">Tipo de Movimiento *</label>
                <select name="tipo_movimiento" id="tipo_movimiento" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
                    <option value="entrada" {{ old('tipo_movimiento') == 'entrada' ? 'selected' : '' }}>Entrada (Aumenta stock y promedia precio)</option>
                    <option value="salida" {{ old('tipo_movimiento') == 'salida' ? 'selected' : '' }}>Salida (Disminuye stock)</option>
                    <option value="ajuste" {{ old('tipo_movimiento') == 'ajuste' ? 'selected' : '' }}>Ajuste (Fijar cantidad)</option>
                </select>
            </div>
            <div>
                <label class="kpi-label" style="display: block;">Cantidad *</label>
                <input type="number" step="0.01" min="0.01" name="cantidad" value="{{ old('cantidad') }}" required style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
            </div>
        </div>

        <div>
            <label class="kpi-label" style="display: block;">Precio Unitario (Obligatorio para entradas)</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 12px; top: 12px; color: var(--muted);">S/</span>
                <input type="number" step="0.01" min="0" name="precio_unitario" id="precio_unitario" value="{{ old('precio_unitario') }}" style="width: 100%; padding: 12px 12px 12px 35px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
            </div>
            <span class="hint" style="display: inline-block; margin-top: 5px;">En salidas, el costo se toma automáticamente del promedio ponderado actual.</span>
        </div>

        <div>
            <label class="kpi-label" style="display: block;">Observaciones / Referencia</label>
            <textarea name="observaciones" rows="3" style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">{{ old('observaciones') }}</textarea>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 10px;">
            <button type="submit" class="pill ok hover:opacity-80 border-0 cursor-pointer" style="padding: 10px 24px; font-size: 14px;">
                <i class="fas fa-save mr-2"></i> Registrar Movimiento
            </button>
            <a href="{{ route('inventario.dashboard') }}" class="pill hover:opacity-80 text-decoration-none" style="padding: 10px 24px; font-size: 14px; border: 1px solid var(--line); color: var(--text);">
                Cancelar
            </a>
        </div>
    </form>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tipoSelect = document.getElementById('tipo_movimiento');
        const precioInput = document.getElementById('precio_unitario');

        function togglePrecio() {
            if (tipoSelect.value === 'salida') {
                precioInput.value = '';
                precioInput.disabled = true;
                precioInput.style.opacity = '0.5';
            } else {
                precioInput.disabled = false;
                precioInput.style.opacity = '1';
                if (tipoSelect.value === 'entrada') {
                    precioInput.setAttribute('required', 'required');
                } else {
                    precioInput.removeAttribute('required');
                }
            }
        }

        tipoSelect.addEventListener('change', togglePrecio);
        togglePrecio(); // Init
    });
</script>
@endpush
@endsection

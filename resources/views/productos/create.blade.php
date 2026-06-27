@extends('layouts.app')

@section('title', 'Nuevo Producto - PISFIL SIG')
@section('header_title', 'Registrar Nuevo Producto')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('productos.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver al Catálogo
    </a>
</div>

<div class="panel stagger-1" style="max-width: 800px;">
    <span class="panel-tag">Formulario</span>
    <div class="panel-head mb-6">
        <h2>Detalles del Nuevo Producto/Material</h2>
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

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Código (SKU)</label>
                <input type="text" name="codigo" required placeholder="Ej. MAT-001" value="{{ old('codigo') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); font-family: var(--font-mono); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Nombre del Producto</label>
                <input type="text" name="nombre" required placeholder="Ej. Plancha de Acero Inox..." value="{{ old('nombre') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Descripción Detallada (Opcional)</label>
            <textarea name="descripcion" rows="3" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">{{ old('descripcion') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Categoría (Opcional)</label>
                <!-- Si tuvieras tabla categorías, sería un select, por ahora un input de texto si no aplica, o déjalo así -->
                <select name="categoria_id" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    <option value="">-- Sin categoría --</option>
                    @if(isset($categorias))
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Unidad de Medida</label>
                <select name="unidad_medida_id" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    <option value="">-- Seleccione (Opcional) --</option>
                    @if(isset($unidades))
                        @foreach($unidades as $uni)
                            <option value="{{ $uni->id }}" {{ old('unidad_medida_id') == $uni->id ? 'selected' : '' }}>{{ $uni->nombre }} ({{ $uni->abreviatura }})</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Tipo (Servicio/Producto)</label>
                <select name="tipo" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    <option value="producto" {{ old('tipo') == 'producto' ? 'selected' : '' }}>Producto / Material</option>
                    <option value="servicio" {{ old('tipo') == 'servicio' ? 'selected' : '' }}>Servicio</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Precio Unitario (Ref.)</label>
                <div style="display: flex; align-items: center; background: var(--surface-1); border: 1px solid var(--line); border-radius: 8px; padding-left: 15px;">
                    <span style="color: var(--muted);">S/</span>
                    <input type="number" step="0.01" name="precio_unitario" value="{{ old('precio_unitario', 0.00) }}" style="width: 100%; padding: 10px; border: none; background: transparent; color: var(--text); outline: none;">
                </div>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Stock Inicial</label>
                <input type="number" step="0.01" name="stock_actual" value="{{ old('stock_actual', 0) }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Stock Mínimo (Alerta)</label>
                <input type="number" step="0.01" name="stock_minimo" value="{{ old('stock_minimo', 5) }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; border-top: 1px solid var(--line); padding-top: 20px;">
            <button type="submit" class="pill ok cursor-pointer text-decoration-none" style="border: none; font-size: 14px; padding: 10px 20px;">
                <i class="fas fa-save"></i> GUARDAR PRODUCTO
            </button>
        </div>
    </form>
</div>
@endsection

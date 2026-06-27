@extends('layouts.app')

@section('title', 'Editar Producto - PISFIL SIG')
@section('header_title', 'Editar Producto')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('productos.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver al Catálogo
    </a>
</div>

<div class="panel stagger-1" style="max-width: 800px;">
    <span class="panel-tag">Formulario</span>
    <div class="panel-head mb-6">
        <h2>Editar Producto: {{ $producto->nombre }}</h2>
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

    <form action="{{ route('productos.update', $producto) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Código (SKU)</label>
                <input type="text" name="codigo" required value="{{ old('codigo', $producto->codigo) }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); font-family: var(--font-mono); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Nombre del Producto</label>
                <input type="text" name="nombre" required value="{{ old('nombre', $producto->nombre) }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Descripción Detallada (Opcional)</label>
            <textarea name="descripcion" rows="3" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Categoría</label>
                <select name="categoria_id" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    <option value="">-- Seleccione categoría --</option>
                    @if(isset($categorias))
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Unidad de Medida</label>
                <select name="unidad_medida_id" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                    <option value="">-- Seleccione unidad --</option>
                    @if(isset($unidades))
                        @foreach($unidades as $uni)
                            <option value="{{ $uni->id }}" {{ old('unidad_medida_id', $producto->unidad_medida_id) == $uni->id ? 'selected' : '' }}>{{ $uni->nombre }} ({{ $uni->abreviatura }})</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Precio Unitario</label>
                <div style="display: flex; align-items: center; background: var(--surface-1); border: 1px solid var(--line); border-radius: 8px; padding-left: 15px;">
                    <span style="color: var(--muted);">S/</span>
                    <input type="number" step="0.01" name="precio_unitario" value="{{ old('precio_unitario', $producto->precio_unitario) }}" style="width: 100%; padding: 10px; border: none; background: transparent; color: var(--text); outline: none;">
                </div>
            </div>
            <div>
                @php $tieneMovimientos = $producto->movimientosKardex()->exists(); @endphp
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Stock Actual</label>
                <input type="number" step="0.01" name="stock_actual" value="{{ old('stock_actual', $producto->stock_actual) }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;" {{ $tieneMovimientos ? 'readonly' : '' }}>
                @if($tieneMovimientos)
                    <div style="font-size: 11px; color: var(--warning); margin-top: 4px;"><i class="fas fa-lock"></i> Gestionado por Kárdex</div>
                @endif
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Stock Mín. (Alerta)</label>
                <input type="number" step="0.01" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>
        
        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Estado</label>
            <select name="estado" required style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
                <option value="activo" {{ old('estado', $producto->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $producto->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; border-top: 1px solid var(--line); padding-top: 20px;">
            <button type="submit" class="pill ok cursor-pointer text-decoration-none" style="border: none; font-size: 14px; padding: 10px 20px;">
                <i class="fas fa-save"></i> ACTUALIZAR PRODUCTO
            </button>
        </div>
    </form>
</div>
@endsection

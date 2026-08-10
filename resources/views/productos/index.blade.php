@extends('layouts.app')

@section('title', 'Catálogo de Productos - PISFIL SIG')
@section('header_title', 'Catálogo de Productos y Almacén')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <button type="button" class="pill ok hover:opacity-80 cursor-pointer" style="font-size: 13px; padding: 8px 16px; border: none;" onclick="abrirModalProducto('modalNuevoProducto')">
        <i class="fas fa-plus"></i> Registrar Nuevo Producto
    </button>
    <a href="{{ route('inventario.dashboard') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Kárdex
    </a>
</div>

@if($errors->any())
    <div class="panel mb-4" style="border-color: rgba(239, 68, 68, 0.35); color: var(--danger);">
        <ul style="margin: 0; padding-left: 18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="panel table-panel stagger-1">
    <div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Catálogo de Materiales y Productos</h2>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">Cod.</th>
                    <th>Nombre del Producto</th>
                    <th>Unidad</th>
                    <th>Stock Actual</th>
                    <th>Stock Min.</th>
                    <th>Precio Ref.</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $prod)
                <tr>
                    <td class="font-mono text-muted">{{ $prod->codigo }}</td>
                    <td style="font-weight: 500;">
                        {{ $prod->nombre }}
                        @if($prod->descripcion)
                            <div style="font-size: 11px; color: var(--muted); font-weight: normal; margin-top: 2px;">{{ Str::limit($prod->descripcion, 30) }}</div>
                        @endif
                    </td>
                    <td>{{ optional($prod->unidadMedida)->abreviatura ?? 'UN' }}</td>
                    <td class="font-mono">
                        @if($prod->stock_actual <= $prod->stock_minimo)
                            <span style="color: var(--danger); font-weight: bold;">{{ number_format($prod->stock_actual, 2) }}</span>
                            <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 10px; margin-left: 5px;"></i>
                        @else
                            {{ number_format($prod->stock_actual, 2) }}
                        @endif
                    </td>
                    <td class="font-mono text-muted">{{ number_format($prod->stock_minimo, 2) }}</td>
                    <td class="font-mono">S/ {{ number_format($prod->precio_unitario, 2) }}</td>
                    <td>
                        @if($prod->estado === 'activo')
                            <span style="color: #10B981; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Activo</span>
                        @else
                            <span style="color: #EF4444; background: rgba(239, 68, 68, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Inactivo</span>
                        @endif
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        @php
                            $productoPayload = $prod->only([
                                'id',
                                'codigo',
                                'nombre',
                                'descripcion',
                                'categoria_id',
                                'unidad_medida_id',
                                'precio_unitario',
                                'stock_actual',
                                'stock_minimo',
                                'estado',
                            ]);
                            $productoPayload['tiene_movimientos'] = (bool) $prod->movimientos_kardex_exists;
                        @endphp
                        <button type="button" class="icon-btn hover:text-primary" title="Editar producto" onclick="abrirModalEditarProducto({{ Illuminate\Support\Js::from($productoPayload) }})">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--muted); padding: 30px;">
                        <i class="fas fa-boxes" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No hay productos registrados en el almacén.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($productos) && $productos->hasPages())
    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--line);">
        {{ $productos->links() }}
    </div>
    @endif
</div>

<style>
    .product-modal-backdrop {
        align-items: center;
        background: rgba(2, 6, 23, 0.68);
        display: none;
        inset: 0;
        justify-content: center;
        padding: 24px;
        position: fixed;
        z-index: 1200;
    }

    .product-modal {
        max-height: calc(100vh - 64px);
        max-width: 840px;
        overflow-y: auto;
        padding: 22px;
        width: min(840px, 100%);
    }

    .product-modal__head,
    .product-modal__actions {
        align-items: center;
        display: flex;
        gap: 12px;
        justify-content: space-between;
    }

    .product-form-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 18px;
    }

    .product-form-grid .full {
        grid-column: 1 / -1;
    }

    .product-form-grid .thirds {
        display: grid;
        gap: 14px;
        grid-column: 1 / -1;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .product-field label {
        color: var(--muted);
        display: block;
        font-size: 12px;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .product-field input,
    .product-field select,
    .product-field textarea {
        background: var(--surface-2);
        border: 1px solid var(--line);
        border-radius: 6px;
        color: var(--text);
        outline: none;
        padding: 9px 10px;
        width: 100%;
    }

    .product-field textarea {
        min-height: 80px;
        resize: vertical;
    }

    .product-field .money-field {
        align-items: center;
        background: var(--surface-2);
        border: 1px solid var(--line);
        border-radius: 6px;
        display: flex;
        padding-left: 10px;
    }

    .product-field .money-field input {
        background: transparent;
        border: none;
    }

    .product-note {
        color: var(--warning);
        display: none;
        font-size: 11px;
        margin-top: 5px;
    }

    @media (max-width: 760px) {
        .product-form-grid,
        .product-form-grid .thirds {
            grid-template-columns: 1fr;
        }

        .product-modal__actions {
            align-items: stretch;
            flex-direction: column-reverse;
        }
    }
</style>

<div id="modalNuevoProducto" class="product-modal-backdrop" data-product-modal>
    <div class="panel product-modal">
        <div class="product-modal__head">
            <div>
                <span class="panel-tag">Nuevo</span>
                <h2 style="font-size: 20px; margin-top: 8px;">Registrar Producto</h2>
            </div>
            <button type="button" class="icon-btn" title="Cerrar" onclick="cerrarModalProducto('modalNuevoProducto')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('productos.store') }}" method="POST">
            @csrf
            <div class="product-form-grid">
                <div class="product-field">
                    <label for="codigo">Codigo SKU</label>
                    <input type="text" id="codigo" name="codigo" required value="{{ old('codigo') }}" placeholder="MAT-001">
                </div>
                <div class="product-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required value="{{ old('nombre') }}">
                </div>
                <div class="product-field full">
                    <label for="descripcion">Descripcion</label>
                    <textarea id="descripcion" name="descripcion">{{ old('descripcion') }}</textarea>
                </div>
                <div class="product-field">
                    <label for="categoria_id">Categoria</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">Seleccione categoria</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="product-field">
                    <label for="unidad_medida_id">Unidad de medida</label>
                    <select id="unidad_medida_id" name="unidad_medida_id" required>
                        <option value="">Seleccione unidad</option>
                        @foreach($unidades as $uni)
                            <option value="{{ $uni->id }}" {{ old('unidad_medida_id') == $uni->id ? 'selected' : '' }}>{{ $uni->nombre }} ({{ $uni->abreviatura }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="thirds">
                    <div class="product-field">
                        <label for="precio_unitario">Precio unitario</label>
                        <div class="money-field">
                            <span style="color: var(--muted);">S/</span>
                            <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" min="0" required value="{{ old('precio_unitario', 0) }}">
                        </div>
                    </div>
                    <div class="product-field">
                        <label for="stock_actual">Stock inicial</label>
                        <input type="number" id="stock_actual" name="stock_actual" step="0.01" min="0" required value="{{ old('stock_actual', 0) }}">
                    </div>
                    <div class="product-field">
                        <label for="stock_minimo">Stock minimo</label>
                        <input type="number" id="stock_minimo" name="stock_minimo" step="1" min="0" required value="{{ old('stock_minimo', 5) }}">
                    </div>
                </div>
                <div class="product-field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="product-modal__actions" style="margin-top: 20px;">
                <button type="button" class="pill cursor-pointer" style="border: 1px solid var(--line); background: transparent; color: var(--text);" onclick="cerrarModalProducto('modalNuevoProducto')">Cancelar</button>
                <button type="submit" class="pill ok cursor-pointer" style="border: none;">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditarProducto" class="product-modal-backdrop" data-product-modal>
    <div class="panel product-modal">
        <div class="product-modal__head">
            <div>
                <span class="panel-tag">Edicion</span>
                <h2 style="font-size: 20px; margin-top: 8px;">Editar Producto</h2>
            </div>
            <button type="button" class="icon-btn" title="Cerrar" onclick="cerrarModalProducto('modalEditarProducto')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formEditarProducto" method="POST">
            @csrf
            @method('PUT')
            <div class="product-form-grid">
                <div class="product-field">
                    <label for="edit_codigo">Codigo SKU</label>
                    <input type="text" id="edit_codigo" name="codigo" required>
                </div>
                <div class="product-field">
                    <label for="edit_nombre">Nombre</label>
                    <input type="text" id="edit_nombre" name="nombre" required>
                </div>
                <div class="product-field full">
                    <label for="edit_descripcion">Descripcion</label>
                    <textarea id="edit_descripcion" name="descripcion"></textarea>
                </div>
                <div class="product-field">
                    <label for="edit_categoria_id">Categoria</label>
                    <select id="edit_categoria_id" name="categoria_id" required>
                        <option value="">Seleccione categoria</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="product-field">
                    <label for="edit_unidad_medida_id">Unidad de medida</label>
                    <select id="edit_unidad_medida_id" name="unidad_medida_id" required>
                        <option value="">Seleccione unidad</option>
                        @foreach($unidades as $uni)
                            <option value="{{ $uni->id }}">{{ $uni->nombre }} ({{ $uni->abreviatura }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="thirds">
                    <div class="product-field">
                        <label for="edit_precio_unitario">Precio unitario</label>
                        <div class="money-field">
                            <span style="color: var(--muted);">S/</span>
                            <input type="number" id="edit_precio_unitario" name="precio_unitario" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="product-field">
                        <label for="edit_stock_actual">Stock actual</label>
                        <input type="number" id="edit_stock_actual" name="stock_actual" step="0.01" min="0" required>
                        <div id="edit_stock_note" class="product-note"><i class="fas fa-lock"></i> Gestionado por Kardex</div>
                    </div>
                    <div class="product-field">
                        <label for="edit_stock_minimo">Stock minimo</label>
                        <input type="number" id="edit_stock_minimo" name="stock_minimo" step="1" min="0" required>
                    </div>
                </div>
                <div class="product-field">
                    <label for="edit_estado">Estado</label>
                    <select id="edit_estado" name="estado">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="product-modal__actions" style="margin-top: 20px;">
                <button type="button" class="pill cursor-pointer" style="border: 1px solid var(--line); background: transparent; color: var(--text);" onclick="cerrarModalProducto('modalEditarProducto')">Cancelar</button>
                <button type="submit" class="pill ok cursor-pointer" style="border: none;">Actualizar Producto</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalProducto(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function cerrarModalProducto(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function setProductoValue(id, value) {
        const field = document.getElementById(id);
        if (field) {
            field.value = value ?? '';
        }
    }

    function abrirModalEditarProducto(producto) {
        setProductoValue('edit_codigo', producto.codigo);
        setProductoValue('edit_nombre', producto.nombre);
        setProductoValue('edit_descripcion', producto.descripcion);
        setProductoValue('edit_categoria_id', producto.categoria_id);
        setProductoValue('edit_unidad_medida_id', producto.unidad_medida_id);
        setProductoValue('edit_precio_unitario', producto.precio_unitario);
        setProductoValue('edit_stock_actual', producto.stock_actual);
        setProductoValue('edit_stock_minimo', producto.stock_minimo);
        setProductoValue('edit_estado', producto.estado || 'activo');

        const stockField = document.getElementById('edit_stock_actual');
        const stockNote = document.getElementById('edit_stock_note');
        if (stockField && stockNote) {
            stockField.readOnly = Boolean(producto.tiene_movimientos);
            stockNote.style.display = producto.tiene_movimientos ? 'block' : 'none';
        }

        document.getElementById('formEditarProducto').action = `/productos/${producto.id}`;
        abrirModalProducto('modalEditarProducto');
    }

    document.querySelectorAll('[data-product-modal]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('[data-product-modal]').forEach((modal) => {
                modal.style.display = 'none';
            });
        }
    });
</script>
@endsection

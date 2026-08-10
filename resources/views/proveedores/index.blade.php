@extends('layouts.app')

@section('title', 'Directorio de Proveedores - PISFIL SIG')
@section('header_title', 'Directorio de Proveedores')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <button type="button" class="pill ok hover:opacity-80 cursor-pointer" style="font-size: 13px; padding: 8px 16px; border: none;" onclick="abrirModalProveedor('modalNuevoProveedor')">
        <i class="fas fa-plus"></i> Nuevo Proveedor
    </button>
    <a href="{{ route('entradas-compra.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Compras
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
        <h2>Proveedores Registrados</h2>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">Cod.</th>
                    <th>RUC</th>
                    <th>Razón Social / Empresa</th>
                    <th>Contacto</th>
                    <th>Celular</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $prov)
                <tr>
                    <td class="font-mono text-muted">{{ $prov->codigo }}</td>
                    <td class="font-mono">{{ $prov->ruc ?? '-' }}</td>
                    <td style="font-weight: 500;">{{ $prov->nombre_empresa }}</td>
                    <td>{{ $prov->nombre_contacto ?? '-' }}</td>
                    <td>{{ $prov->celular ?? '-' }}</td>
                    <td>
                        @if($prov->estado)
                            <span style="color: #10B981; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Activo</span>
                        @else
                            <span style="color: #EF4444; background: rgba(239, 68, 68, 0.1); padding: 4px 8px; border-radius: 4px; font-size: 11px;">Inactivo</span>
                        @endif
                    </td>
                    <td style="text-align: right; white-space: nowrap;">
                        <button type="button" class="icon-btn hover:text-primary" title="Editar proveedor" onclick="abrirModalEditarProveedor({{ Illuminate\Support\Js::from($prov) }})">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--muted); padding: 30px;">
                        <i class="fas fa-address-book" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i><br>
                        No hay proveedores registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($proveedores->hasPages())
    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--line);">
        {{ $proveedores->links() }}
    </div>
    @endif
</div>

<style>
    .provider-modal-backdrop {
        align-items: center;
        background: rgba(2, 6, 23, 0.68);
        display: none;
        inset: 0;
        justify-content: center;
        padding: 24px;
        position: fixed;
        z-index: 1200;
    }

    .provider-modal {
        max-height: calc(100vh - 64px);
        max-width: 820px;
        overflow-y: auto;
        padding: 22px;
        width: min(820px, 100%);
    }

    .provider-modal__head,
    .provider-modal__actions {
        align-items: center;
        display: flex;
        gap: 12px;
        justify-content: space-between;
    }

    .provider-form-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 18px;
    }

    .provider-form-grid .full {
        grid-column: 1 / -1;
    }

    .provider-field label {
        color: var(--muted);
        display: block;
        font-size: 12px;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .provider-field input,
    .provider-field select,
    .provider-field textarea {
        background: var(--surface-2);
        border: 1px solid var(--line);
        border-radius: 6px;
        color: var(--text);
        outline: none;
        padding: 9px 10px;
        width: 100%;
    }

    .provider-field textarea {
        min-height: 76px;
        resize: vertical;
    }

    @media (max-width: 720px) {
        .provider-form-grid {
            grid-template-columns: 1fr;
        }

        .provider-modal__actions {
            align-items: stretch;
            flex-direction: column-reverse;
        }
    }
</style>

<div id="modalNuevoProveedor" class="provider-modal-backdrop" data-provider-modal>
    <div class="panel provider-modal">
        <div class="provider-modal__head">
            <div>
                <span class="panel-tag">Nuevo</span>
                <h2 style="font-size: 20px; margin-top: 8px;">Registrar Proveedor</h2>
            </div>
            <button type="button" class="icon-btn" title="Cerrar" onclick="cerrarModalProveedor('modalNuevoProveedor')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('proveedores.store') }}" method="POST">
            @csrf
            <div class="provider-form-grid">
                <div class="provider-field">
                    <label for="codigo">Codigo</label>
                    <input type="text" id="codigo" name="codigo" required value="{{ old('codigo') }}">
                </div>
                <div class="provider-field">
                    <label for="ruc">RUC</label>
                    <input type="text" id="ruc" name="ruc" value="{{ old('ruc') }}">
                </div>
                <div class="provider-field full">
                    <label for="nombre_empresa">Razon social / empresa</label>
                    <input type="text" id="nombre_empresa" name="nombre_empresa" required value="{{ old('nombre_empresa') }}">
                </div>
                <div class="provider-field">
                    <label for="nombre_contacto">Contacto</label>
                    <input type="text" id="nombre_contacto" name="nombre_contacto" value="{{ old('nombre_contacto') }}">
                </div>
                <div class="provider-field">
                    <label for="documento_identidad">Documento</label>
                    <input type="text" id="documento_identidad" name="documento_identidad" value="{{ old('documento_identidad') }}">
                </div>
                <div class="provider-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="provider-field">
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}">
                </div>
                <div class="provider-field">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular" value="{{ old('celular') }}">
                </div>
                <div class="provider-field">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad') }}">
                </div>
                <div class="provider-field">
                    <label for="pais">Pais</label>
                    <input type="text" id="pais" name="pais" value="{{ old('pais', 'Peru') }}">
                </div>
                <div class="provider-field">
                    <label for="condicion_pago">Condicion de pago</label>
                    <input type="text" id="condicion_pago" name="condicion_pago" value="{{ old('condicion_pago') }}">
                </div>
                <div class="provider-field">
                    <label for="plazo_entrega">Plazo entrega (dias)</label>
                    <input type="number" id="plazo_entrega" name="plazo_entrega" min="0" value="{{ old('plazo_entrega') }}">
                </div>
                <div class="provider-field full">
                    <label for="direccion">Direccion</label>
                    <textarea id="direccion" name="direccion">{{ old('direccion') }}</textarea>
                </div>
            </div>

            <div class="provider-modal__actions" style="margin-top: 20px;">
                <button type="button" class="pill cursor-pointer" style="border: 1px solid var(--line); background: transparent; color: var(--text);" onclick="cerrarModalProveedor('modalNuevoProveedor')">Cancelar</button>
                <button type="submit" class="pill ok cursor-pointer" style="border: none;">Guardar Proveedor</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditarProveedor" class="provider-modal-backdrop" data-provider-modal>
    <div class="panel provider-modal">
        <div class="provider-modal__head">
            <div>
                <span class="panel-tag">Edicion</span>
                <h2 style="font-size: 20px; margin-top: 8px;">Editar Proveedor</h2>
            </div>
            <button type="button" class="icon-btn" title="Cerrar" onclick="cerrarModalProveedor('modalEditarProveedor')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formEditarProveedor" method="POST">
            @csrf
            @method('PUT')
            <div class="provider-form-grid">
                <div class="provider-field">
                    <label for="edit_codigo">Codigo</label>
                    <input type="text" id="edit_codigo" name="codigo" required>
                </div>
                <div class="provider-field">
                    <label for="edit_ruc">RUC</label>
                    <input type="text" id="edit_ruc" name="ruc">
                </div>
                <div class="provider-field full">
                    <label for="edit_nombre_empresa">Razon social / empresa</label>
                    <input type="text" id="edit_nombre_empresa" name="nombre_empresa" required>
                </div>
                <div class="provider-field">
                    <label for="edit_nombre_contacto">Contacto</label>
                    <input type="text" id="edit_nombre_contacto" name="nombre_contacto">
                </div>
                <div class="provider-field">
                    <label for="edit_documento_identidad">Documento</label>
                    <input type="text" id="edit_documento_identidad" name="documento_identidad">
                </div>
                <div class="provider-field">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email">
                </div>
                <div class="provider-field">
                    <label for="edit_telefono">Telefono</label>
                    <input type="text" id="edit_telefono" name="telefono">
                </div>
                <div class="provider-field">
                    <label for="edit_celular">Celular</label>
                    <input type="text" id="edit_celular" name="celular">
                </div>
                <div class="provider-field">
                    <label for="edit_ciudad">Ciudad</label>
                    <input type="text" id="edit_ciudad" name="ciudad">
                </div>
                <div class="provider-field">
                    <label for="edit_pais">Pais</label>
                    <input type="text" id="edit_pais" name="pais">
                </div>
                <div class="provider-field">
                    <label for="edit_condicion_pago">Condicion de pago</label>
                    <input type="text" id="edit_condicion_pago" name="condicion_pago">
                </div>
                <div class="provider-field">
                    <label for="edit_plazo_entrega">Plazo entrega (dias)</label>
                    <input type="number" id="edit_plazo_entrega" name="plazo_entrega" min="0">
                </div>
                <div class="provider-field">
                    <label for="edit_estado">Estado</label>
                    <select id="edit_estado" name="estado">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="provider-field full">
                    <label for="edit_direccion">Direccion</label>
                    <textarea id="edit_direccion" name="direccion"></textarea>
                </div>
            </div>

            <div class="provider-modal__actions" style="margin-top: 20px;">
                <button type="button" class="pill cursor-pointer" style="border: 1px solid var(--line); background: transparent; color: var(--text);" onclick="cerrarModalProveedor('modalEditarProveedor')">Cancelar</button>
                <button type="submit" class="pill ok cursor-pointer" style="border: none;">Actualizar Proveedor</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalProveedor(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function cerrarModalProveedor(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function setProveedorValue(id, value) {
        const field = document.getElementById(id);
        if (field) {
            field.value = value ?? '';
        }
    }

    function abrirModalEditarProveedor(proveedor) {
        setProveedorValue('edit_codigo', proveedor.codigo);
        setProveedorValue('edit_ruc', proveedor.ruc);
        setProveedorValue('edit_nombre_empresa', proveedor.nombre_empresa);
        setProveedorValue('edit_nombre_contacto', proveedor.nombre_contacto);
        setProveedorValue('edit_documento_identidad', proveedor.documento_identidad);
        setProveedorValue('edit_email', proveedor.email);
        setProveedorValue('edit_telefono', proveedor.telefono);
        setProveedorValue('edit_celular', proveedor.celular);
        setProveedorValue('edit_ciudad', proveedor.ciudad);
        setProveedorValue('edit_pais', proveedor.pais);
        setProveedorValue('edit_condicion_pago', proveedor.condicion_pago);
        setProveedorValue('edit_plazo_entrega', proveedor.plazo_entrega);
        setProveedorValue('edit_direccion', proveedor.direccion);
        setProveedorValue('edit_estado', proveedor.estado ? '1' : '0');

        document.getElementById('formEditarProveedor').action = `/proveedores/${proveedor.id}`;
        abrirModalProveedor('modalEditarProveedor');
    }

    document.querySelectorAll('[data-provider-modal]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('[data-provider-modal]').forEach((modal) => {
                modal.style.display = 'none';
            });
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Registro de Clientes - PISFIL SIG')
@section('header_title', 'Cartera de Clientes')

@section('content')
<div class="panel-head mb-4">
    <button class="pill ok cursor-pointer" onclick="document.getElementById('modalNuevoCliente').style.display = 'flex'">
        <i class="fas fa-plus"></i> Nuevo Cliente
    </button>
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

<section class="panel table-panel stagger-1">
    <span class="panel-tag">Directorio</span>
    <div class="panel-head mb-4">
        <h2>Listado de Clientes</h2>
    </div>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Nombre / Razón Social</th>
                    <th>RUC/DNI</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>
                            <div style="font-weight: 500; font-size: 14px;">{{ $cliente->nombre }}</div>
                            <div style="font-size: 11px; color: var(--muted);">{{ $cliente->direccion ?? 'Sin dirección' }}</div>
                        </td>
                        <td class="mono" style="color: var(--muted);">{{ $cliente->documento_identidad }}</td>
                        <td>
                            <div style="font-size: 12px;"><i class="fas fa-phone mr-1 text-muted"></i> {{ $cliente->telefono ?? '-' }}</div>
                            <div style="font-size: 12px;"><i class="fas fa-envelope mr-1 text-muted"></i> {{ $cliente->email ?? '-' }}</div>
                        </td>
                        <td>
                            @if($cliente->estado)
                                <span class="pill ok" style="padding: 2px 8px; font-size: 11px;">Activo</span>
                            @else
                                <span class="pill danger" style="padding: 2px 8px; font-size: 11px;">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="icon-btn hover:text-primary" title="Editar" onclick="abrirModalEditar({{ $cliente->toJson() }})">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--muted); padding: 20px;">
                            No hay clientes registrados en la base de datos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 20px;">
        {{ $clientes->links('pagination::tailwind') }}
    </div>
</section>

<!-- Modal Nuevo Cliente -->
<div id="modalNuevoCliente" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="panel" style="width: 100%; max-width: 500px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">Nuevo Cliente</h2>
            <button onclick="document.getElementById('modalNuevoCliente').style.display = 'none'" class="icon-btn" style="background: none; border: none; color: var(--muted); cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="{{ route('clientes.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            @csrf
            <div>
                <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Nombre o Razón Social</label>
                <input type="text" name="nombre" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">RUC / DNI</label>
                <input type="text" name="documento_identidad" required inputmode="numeric" pattern="[0-9]{8}|[0-9]{11}" minlength="8" maxlength="11" title="Ingrese DNI de 8 digitos o RUC de 11 digitos" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Dirección</label>
                <input type="text" name="direccion" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Teléfono</label>
                    <input type="text" name="telefono" inputmode="tel" pattern="[0-9+() -]{6,20}" maxlength="20" title="Solo numeros y signos telefonicos" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Email</label>
                    <input type="email" name="email" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('modalNuevoCliente').style.display = 'none'" class="pill hover:bg-surface-2 cursor-pointer" style="border: 1px solid var(--line); background: transparent; color: var(--text);">Cancelar</button>
                <button type="submit" class="pill ok cursor-pointer" style="border: none;">Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Cliente -->
<div id="modalEditarCliente" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="panel" style="width: 100%; max-width: 500px; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 18px;">Editar Cliente</h2>
            <button onclick="document.getElementById('modalEditarCliente').style.display = 'none'" class="icon-btn" style="background: none; border: none; color: var(--muted); cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="formEditarCliente" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            @csrf
            @method('PUT')
            <div>
                <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Nombre o Razón Social</label>
                <input type="text" name="nombre" id="edit_nombre" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">RUC / DNI</label>
                <input type="text" name="documento_identidad" id="edit_documento_identidad" required inputmode="numeric" pattern="[0-9]{8}|[0-9]{11}" minlength="8" maxlength="11" title="Ingrese DNI de 8 digitos o RUC de 11 digitos" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Dirección</label>
                <input type="text" name="direccion" id="edit_direccion" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono" inputmode="tel" pattern="[0-9+() -]{6,20}" maxlength="20" title="Solo numeros y signos telefonicos" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Email</label>
                    <input type="email" name="email" id="edit_email" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Estado</label>
                <select name="estado" id="edit_estado" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('modalEditarCliente').style.display = 'none'" class="pill hover:bg-surface-2 cursor-pointer" style="border: 1px solid var(--line); background: transparent; color: var(--text);">Cancelar</button>
                <button type="submit" class="pill ok cursor-pointer" style="border: none;">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalEditar(cliente) {
        document.getElementById('edit_nombre').value = cliente.nombre;
        document.getElementById('edit_documento_identidad').value = cliente.documento_identidad;
        document.getElementById('edit_direccion').value = cliente.direccion || '';
        document.getElementById('edit_telefono').value = cliente.telefono || '';
        document.getElementById('edit_email').value = cliente.email || '';
        document.getElementById('edit_estado').value = cliente.estado ? '1' : '0';
        
        document.getElementById('formEditarCliente').action = `/clientes/${cliente.id}`;
        document.getElementById('modalEditarCliente').style.display = 'flex';
    }
</script>
@endsection

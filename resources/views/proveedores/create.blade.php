@extends('layouts.app')

@section('title', 'Nuevo Proveedor - PISFIL SIG')
@section('header_title', 'Nuevo Proveedor')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('proveedores.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Proveedores
    </a>
</div>

<div class="panel stagger-1" style="max-width: 800px;">
    <span class="panel-tag">Formulario</span>
    <div class="panel-head mb-6">
        <h2>Registrar Nuevo Proveedor</h2>
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

    <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Código Interno</label>
                <input type="text" name="codigo" required placeholder="PRV-001" value="{{ old('codigo') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); font-family: var(--font-mono); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">RUC (Opcional)</label>
                <input type="text" name="ruc" placeholder="1045..." value="{{ old('ruc') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Razón Social / Nombre de la Empresa</label>
            <input type="text" name="nombre_empresa" required placeholder="Ej. Comercializadora XYZ" value="{{ old('nombre_empresa') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Nombre de Contacto (Vendedor)</label>
                <input type="text" name="nombre_contacto" placeholder="Ej. Juan Pérez" value="{{ old('nombre_contacto') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">DNI de Contacto</label>
                <input type="text" name="documento_identidad" placeholder="..." value="{{ old('documento_identidad') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Email</label>
                <input type="email" name="email" placeholder="ventas@xyz.com" value="{{ old('email') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Teléfono Fijo</label>
                <input type="text" name="telefono" placeholder="..." value="{{ old('telefono') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Celular / WhatsApp</label>
                <input type="text" name="celular" placeholder="..." value="{{ old('celular') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Dirección Física</label>
            <input type="text" name="direccion" placeholder="Av. Principal 123..." value="{{ old('direccion') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Ciudad</label>
                <input type="text" name="ciudad" placeholder="Lima" value="{{ old('ciudad') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">Estado / Región</label>
                <input type="text" name="estado_region" placeholder="Lima" value="{{ old('estado_region') }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; color: var(--muted); font-size: 13px;">País</label>
                <input type="text" name="pais" placeholder="Perú" value="{{ old('pais') ?? 'Perú' }}" style="width: 100%; padding: 10px 15px; border-radius: 8px; background: var(--surface-1); border: 1px solid var(--line); color: var(--text); outline: none;">
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; border-top: 1px solid var(--line); padding-top: 20px;">
            <button type="submit" class="pill ok cursor-pointer text-decoration-none" style="border: none; font-size: 14px; padding: 10px 20px;">
                <i class="fas fa-save"></i> GUARDAR PROVEEDOR
            </button>
        </div>
    </form>
</div>
@endsection

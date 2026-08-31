@extends('layouts.app')

@section('title', 'Nueva Cuenta Contable')
@section('header_title', 'Crear Nueva Cuenta Contable')

@section('content')
<div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('cuentas-contables.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
</div>

<div class="panel stagger-1" style="max-width: 600px; margin: 0 auto;">
    <span class="panel-tag">Detalles de la Cuenta</span>
    
    <form action="{{ route('cuentas-contables.store') }}" method="POST" style="margin-top: 20px;">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted);">Código (ej. 1041)</label>
            <input type="text" name="codigo" required inputmode="numeric" pattern="[0-9]{2,20}" maxlength="20" title="Ingrese solo numeros" value="{{ old('codigo') }}" style="width: 100%; padding: 10px; background-color: var(--bg); border: 1px solid var(--line); color: var(--text); border-radius: 6px;">
            @error('codigo') <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted);">Descripción</label>
            <input type="text" name="descripcion" required value="{{ old('descripcion') }}" style="width: 100%; padding: 10px; background-color: var(--bg); border: 1px solid var(--line); color: var(--text); border-radius: 6px;">
            @error('descripcion') <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted);">Elemento (1-9, 0)</label>
                <input type="text" name="elemento" required inputmode="numeric" pattern="[0-9]{1,5}" maxlength="5" title="Ingrese solo numeros" value="{{ old('elemento') }}" style="width: 100%; padding: 10px; background-color: var(--bg); border: 1px solid var(--line); color: var(--text); border-radius: 6px;">
                @error('elemento') <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span> @enderror
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted);">Nivel (Dígitos)</label>
                <input type="number" name="nivel" min="2" max="6" required value="{{ old('nivel', 3) }}" style="width: 100%; padding: 10px; background-color: var(--bg); border: 1px solid var(--line); color: var(--text); border-radius: 6px;">
                @error('nivel') <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted);">Cuenta Padre (Opcional)</label>
            <select name="padre_id" style="width: 100%; padding: 10px; background-color: var(--bg); border: 1px solid var(--line); color: var(--text); border-radius: 6px;">
                <option value="">-- Sin cuenta padre (Cuenta Principal) --</option>
                @foreach($cuentasPrincipales as $padre)
                    <option value="{{ $padre->id }}" {{ old('padre_id') == $padre->id ? 'selected' : '' }}>
                        {{ $padre->codigo }} - {{ $padre->descripcion }}
                    </option>
                @endforeach
            </select>
            @error('padre_id') <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span> @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-size: 13px; color: var(--muted);">Tipo (Activo, Pasivo, Gasto...)</label>
            <select name="tipo" style="width: 100%; padding: 10px; background-color: var(--bg); border: 1px solid var(--line); color: var(--text); border-radius: 6px;">
                <option value="">-- Seleccione tipo --</option>
                @foreach(['Activo', 'Pasivo', 'Patrimonio', 'Gasto', 'Ingreso'] as $tipo)
                    <option value="{{ $tipo }}" {{ old('tipo') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                @endforeach
            </select>
            @error('tipo') <span style="color: var(--danger); font-size: 12px;">{{ $message }}</span> @enderror
        </div>

        <input type="hidden" name="estado" value="1">

        <div style="text-align: right;">
            <button type="submit" class="pill hover:opacity-80 cursor-pointer" style="padding: 10px 20px; font-size: 14px; background-color: var(--primary); color: #fff; border: none;">
                Guardar Cuenta
            </button>
        </div>
    </form>
</div>
@endsection

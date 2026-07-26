@extends('layouts.app')

@section('title', 'Gestión de Cuentas Contables')
@section('header_title', 'Módulo de Configuración Contable (CRUD)')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px; justify-content: space-between; align-items: center;">
    <div>
        <a href="{{ route('contabilidad.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
            <i class="fas fa-arrow-left"></i> Volver a Contabilidad
        </a>
    </div>
    <div>
        <a href="{{ route('cuentas-contables.create') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; background-color: var(--primary); color: #fff;">
            <i class="fas fa-plus"></i> Nueva Cuenta
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background-color: rgba(46, 213, 115, 0.1); border: 1px solid var(--success); color: var(--success); padding: 10px 15px; border-radius: 6px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background-color: rgba(255, 71, 87, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 10px 15px; border-radius: 6px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
@endif

<div class="panel stagger-1">
    <span class="panel-tag">Listado Maestro de Cuentas</span>
    
    <div class="table-responsive" style="margin-top: 15px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--line);">
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Código</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Descripción</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Nivel</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Depende de</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase; text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cuentas as $cuenta)
                    <tr style="border-bottom: 1px solid var(--line); {{ $cuenta->nivel == 2 ? 'background-color: rgba(255, 255, 255, 0.02);' : '' }}">
                        <td style="padding: 12px 15px; {{ $cuenta->nivel == 2 ? 'font-weight: bold; color: var(--primary);' : 'color: var(--text); padding-left: ' . ($cuenta->nivel * 10) . 'px;' }}">
                            {{ $cuenta->codigo }}
                        </td>
                        <td style="padding: 12px 15px; {{ $cuenta->nivel == 2 ? 'font-weight: bold;' : 'color: var(--muted);' }}">
                            {{ $cuenta->descripcion }}
                        </td>
                        <td style="padding: 12px 15px; color: var(--text); font-size: 13px;">{{ $cuenta->nivel }} d</td>
                        <td style="padding: 12px 15px; color: var(--text); font-size: 13px;">
                            @if($cuenta->padre)
                                {{ $cuenta->padre->codigo }}
                            @else
                                <span style="color: var(--muted);">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px 15px; text-align: right; display: flex; gap: 5px; justify-content: flex-end;">
                            <a href="{{ route('cuentas-contables.edit', $cuenta->id) }}" class="pill hover:opacity-80" style="padding: 4px 8px; font-size: 14px; background-color: var(--surface-1); color: var(--text); text-decoration: none;" title="Editar">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('cuentas-contables.destroy', $cuenta->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta cuenta?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="pill hover:opacity-80 cursor-pointer" style="padding: 4px 8px; font-size: 14px; background-color: rgba(255, 71, 87, 0.1); border: 1px solid rgba(255, 71, 87, 0.3); color: var(--danger);" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

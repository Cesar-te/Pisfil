@extends('layouts.app')

@section('title', 'Plan de Cuentas - PCGE')
@section('header_title', 'Plan Contable General Empresarial (PCGE)')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px; justify-content: space-between; align-items: center;">
    <div>
        <a href="{{ route('contabilidad.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
            <i class="fas fa-arrow-left"></i> Volver a Contabilidad
        </a>
    </div>
    <div style="font-size: 13px; color: var(--muted);">
        Catálogo PCGE (2 y 3 dígitos)
    </div>
</div>

<div class="panel stagger-1">
    <span class="panel-tag">Catálogo de Cuentas</span>
    
    <div class="table-responsive" style="margin-top: 15px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--line);">
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Código</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Descripción</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Elemento</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Nivel</th>
                    <th style="padding: 12px 15px; color: var(--muted); font-size: 12px; font-weight: 500; text-transform: uppercase;">Tipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cuentasPrincipales as $cuenta)
                    <tr style="border-bottom: 1px solid var(--line); background-color: rgba(255, 255, 255, 0.02);">
                        <td style="padding: 12px 15px; font-weight: bold; color: var(--primary);">{{ $cuenta->codigo }}</td>
                        <td style="padding: 12px 15px; font-weight: bold;">{{ $cuenta->descripcion }}</td>
                        <td style="padding: 12px 15px; color: var(--text);">{{ $cuenta->elemento }}</td>
                        <td style="padding: 12px 15px; color: var(--text);">Cuenta ({{ $cuenta->nivel }} d)</td>
                        <td style="padding: 12px 15px; color: var(--text);">
                            <span style="background-color: var(--surface-1); padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $cuenta->tipo }}</span>
                        </td>
                    </tr>
                    
                    @if($cuenta->subcuentas->count() > 0)
                        @foreach($cuenta->subcuentas as $subcuenta)
                        <tr style="border-bottom: 1px solid var(--line); font-size: 13px;">
                            <td style="padding: 10px 15px; padding-left: 30px; color: var(--muted);">{{ $subcuenta->codigo }}</td>
                            <td style="padding: 10px 15px; color: var(--muted);">{{ $subcuenta->descripcion }}</td>
                            <td style="padding: 10px 15px; color: var(--muted);">{{ $subcuenta->elemento }}</td>
                            <td style="padding: 10px 15px; color: var(--muted);">Subcuenta ({{ $subcuenta->nivel }} d)</td>
                            <td style="padding: 10px 15px; color: var(--muted);"></td>
                        </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

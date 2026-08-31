@extends('layouts.app')

@section('title', 'Proveedor - PISFIL SIG')
@section('header_title', 'Detalle del Proveedor')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('proveedores.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
    <a href="{{ route('proveedores.edit', $proveedor) }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-pen"></i> Editar
    </a>
</div>

<section class="panel mb-4">
    <span class="panel-tag">{{ $proveedor->codigo }}</span>
    <div class="panel-head mb-4">
        <h2>{{ $proveedor->nombre_empresa }}</h2>
    </div>

    <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="kpi-card">
            <span class="kpi-label">RUC</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $proveedor->ruc ?? '-' }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Contacto</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $proveedor->nombre_contacto ?? '-' }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Telefono</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $proveedor->telefono ?? $proveedor->celular ?? '-' }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Estado</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $proveedor->estado ? 'Activo' : 'Inactivo' }}</span>
        </div>
    </div>
</section>

<section class="panel table-panel">
    <div class="panel-head mb-4">
        <h2>Compras registradas</h2>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Pago</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedor->entradas as $entrada)
                    <tr>
                        <td>{{ $entrada->numero_documento }}</td>
                        <td>{{ optional($entrada->fecha_emision)->format('d/m/Y') ?? $entrada->fecha_emision }}</td>
                        <td>{{ ucfirst($entrada->estado) }}</td>
                        <td>{{ ucfirst($entrada->estado_pago) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--muted); padding: 20px;">Sin compras registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('title', 'Cuenta Contable')
@section('header_title', 'Detalle de Cuenta Contable')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('cuentas-contables.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
    <a href="{{ route('cuentas-contables.edit', $cuentaContable) }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-pen"></i> Editar
    </a>
</div>

<section class="panel">
    <span class="panel-tag">{{ $cuentaContable->codigo }}</span>
    <div class="panel-head mb-4">
        <h2>{{ $cuentaContable->descripcion }}</h2>
    </div>

    <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="kpi-card">
            <span class="kpi-label">Elemento</span>
            <span class="kpi-value mono">{{ $cuentaContable->elemento }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Nivel</span>
            <span class="kpi-value mono">{{ $cuentaContable->nivel }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Tipo</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $cuentaContable->tipo ?? '-' }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Cuenta padre</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $cuentaContable->padre->codigo ?? '-' }}</span>
        </div>
    </div>
</section>
@endsection

@extends('layouts.app')

@section('title', 'Producto - PISFIL SIG')
@section('header_title', 'Detalle del Producto')

@section('content')
<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('productos.index') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
    <a href="{{ route('productos.edit', $producto) }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-pen"></i> Editar
    </a>
</div>

<section class="panel">
    <span class="panel-tag">{{ $producto->codigo }}</span>
    <div class="panel-head mb-4">
        <h2>{{ $producto->nombre }}</h2>
    </div>

    <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="kpi-card">
            <span class="kpi-label">Categoria</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $producto->categoria->nombre ?? 'N/A' }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Unidad</span>
            <span class="kpi-value" style="font-size: 18px;">{{ $producto->unidadMedida->simbolo ?? 'N/A' }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Stock actual</span>
            <span class="kpi-value mono">{{ number_format((float) $producto->stock_actual, 2) }}</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Precio unitario</span>
            <span class="kpi-value mono">S/ {{ number_format((float) $producto->precio_unitario, 2) }}</span>
        </div>
    </div>

    @if($producto->descripcion)
        <div style="margin-top: 20px; color: var(--muted);">
            {{ $producto->descripcion }}
        </div>
    @endif
</section>
@endsection

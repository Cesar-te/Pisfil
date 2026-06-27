@extends('layouts.app')

@section('title', 'Alertas de Stock - PISFIL SIG')
@section('header_title', 'Reporte de Stock Crítico')

@section('content')

<div class="panel-head mb-4" style="display: flex; gap: 10px;">
    <a href="{{ route('inventario.dashboard') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="font-size: 13px; padding: 8px 16px; border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>
</div>

<section class="panel table-panel stagger-1">
    <span class="panel-tag" style="border-color: var(--secondary); color: var(--secondary);">Crítico</span>
    <div class="panel-head">
        <div>
            <h2 style="color: var(--secondary);"><i class="fas fa-triangle-exclamation mr-2"></i> Productos por debajo del Stock Mínimo</h2>
            <p style="color: var(--muted); font-size: 13.5px; margin-top: 4px;">Estos materiales requieren reabastecimiento urgente para evitar quiebres de inventario o paros en producción.</p>
        </div>
    </div>

    <div style="overflow-x: auto; margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto / Material</th>
                    <th>Categoría</th>
                    <th>Stock Mínimo</th>
                    <th>Stock Actual</th>
                    <th>Déficit</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $prod)
                <tr style="background: rgba(226, 114, 46, 0.03);">
                    <td class="mono">{{ $prod->codigo }}</td>
                    <td style="font-weight: 500; color: var(--text);">{{ $prod->nombre }}</td>
                    <td><span class="hint">{{ $prod->categoria->nombre ?? 'N/A' }}</span></td>
                    <td style="font-family: var(--font-mono); color: var(--muted);">{{ $prod->stock_minimo }}</td>
                    <td style="font-family: var(--font-mono); font-weight: bold; color: var(--danger);">{{ $prod->stock_actual }}</td>
                    <td style="font-family: var(--font-mono); font-weight: bold; color: var(--secondary);">
                        {{ $prod->stock_minimo - $prod->stock_actual }}
                    </td>
                    <td>
                        <a href="{{ route('inventario.create_movimiento', ['producto_id' => $prod->id]) }}" class="pill pending text-decoration-none hover:opacity-80">
                            <i class="fas fa-cart-plus mr-1"></i> Reabastecer
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        <i class="fas fa-check-circle" style="font-size: 48px; color: var(--success); margin-bottom: 16px; opacity: 0.8;"></i>
                        <h3 style="font-family: var(--font-display); color: var(--success);">Stock Saludable</h3>
                        <p style="color: var(--muted); margin-top: 8px;">No hay ningún producto por debajo de su stock mínimo.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $productos->links('pagination::tailwind') }}
    </div>
</section>

@endsection

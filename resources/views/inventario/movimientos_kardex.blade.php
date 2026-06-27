@extends('layouts.app')

@section('title', 'Movimientos de Kárdex - PISFIL SIG')
@section('header_title', 'Movimientos de Kárdex')

@section('content')

<!-- Filtros -->
<section class="panel stagger-1 mb-8" style="padding: 20px;">
    <form method="GET" action="{{ route('inventario.movimientos_kardex') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label class="kpi-label">Producto</label>
            <select name="producto_id" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
                <option value="">Todos los productos</option>
                @foreach($productos as $id => $nombre)
                    <option value="{{ $id }}" {{ request('producto_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label class="kpi-label">Tipo Movimiento</label>
            <select name="tipo_movimiento" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
                <option value="">Todos</option>
                <option value="entrada" {{ request('tipo_movimiento') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                <option value="salida" {{ request('tipo_movimiento') == 'salida' ? 'selected' : '' }}>Salida</option>
                <option value="ajuste" {{ request('tipo_movimiento') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
            </select>
        </div>
        <div>
            <label class="kpi-label">Desde</label>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" style="padding: 8px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
        </div>
        <div>
            <label class="kpi-label">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" style="padding: 8px; border-radius: 6px; border: 1px solid var(--line); background: var(--surface-2); color: var(--text);">
        </div>
        <div>
            <button type="submit" class="pill ok hover:opacity-80 cursor-pointer border-0" style="padding: 8px 20px; height: 38px;">
                <i class="fas fa-search mr-2"></i> Filtrar
            </button>
            <a href="{{ route('inventario.movimientos_kardex') }}" class="pill hover:opacity-80 text-decoration-none" style="padding: 8px 20px; height: 38px; border: 1px solid var(--line); color: var(--text);">
                Limpiar
            </a>
        </div>
    </form>
</section>

<!-- Tabla de Movimientos -->
<section class="panel table-panel stagger-2">
    <span class="panel-tag">Reporte</span>
    <div class="panel-head">
        <h2>Historial de Movimientos</h2>
        <a href="{{ route('inventario.create_movimiento') }}" class="pill ok text-decoration-none hover:opacity-80">
            <i class="fas fa-plus"></i> Nuevo Movimiento
        </a>
    </div>
    
    @if(session('success'))
        <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
            {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo U.</th>
                    <th>Saldo Ant.</th>
                    <th>Saldo Act.</th>
                    <th>Registrado por</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $mov)
                <tr>
                    <td style="font-size: 12px;">{{ $mov->fecha_movimiento->format('Y-m-d H:i') }}</td>
                    <td class="mono">{{ $mov->producto->nombre }}</td>
                    <td>
                        @if($mov->tipo_movimiento === 'entrada')
                            <span class="pill ok">Entrada</span>
                        @elseif($mov->tipo_movimiento === 'salida')
                            <span class="pill danger">Salida</span>
                        @else
                            <span class="pill pending">{{ ucfirst($mov->tipo_movimiento) }}</span>
                        @endif
                    </td>
                    <td style="font-family: var(--font-mono); font-weight: 600; {{ $mov->tipo_movimiento === 'salida' ? 'color: var(--danger);' : 'color: var(--success);' }}">
                        {{ $mov->tipo_movimiento === 'salida' ? '-' : '+' }}{{ $mov->cantidad }}
                    </td>
                    <td style="color: var(--primary);">S/ {{ number_format($mov->precio_unitario, 2) }}</td>
                    <td style="color: var(--muted);">{{ $mov->saldo_anterior }}</td>
                    <td style="font-weight: bold;">{{ $mov->saldo_actual }}</td>
                    <td><span class="hint">{{ $mov->usuario->name ?? 'N/A' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--muted);">No se encontraron movimientos con los filtros aplicados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $movimientos->links('pagination::tailwind') }}
    </div>
</section>

@endsection

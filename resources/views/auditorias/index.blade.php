@extends('layouts.app')

@section('title', 'Auditoria - PISFIL SIG')
@section('header_title', 'Auditoria de Operaciones')

@section('content')
<section class="panel mb-8" style="background: var(--surface-2); padding: 15px 20px; border-bottom: 2px solid var(--primary);">
    <form action="{{ route('auditorias.index') }}" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Accion</label>
            <select name="accion" style="padding: 8px 15px; border-radius: 5px; background: var(--bg); border: 1px solid var(--line); color: var(--text); min-width: 180px;">
                <option value="">Todas</option>
                @foreach($acciones as $accion)
                    <option value="{{ $accion }}" {{ request('accion') === $accion ? 'selected' : '' }}>{{ $accion }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Entidad</label>
            <select name="entidad" style="padding: 8px 15px; border-radius: 5px; background: var(--bg); border: 1px solid var(--line); color: var(--text); min-width: 160px;">
                <option value="">Todas</option>
                @foreach($entidades as $entidad)
                    <option value="{{ $entidad }}" {{ request('entidad') === $entidad ? 'selected' : '' }}>{{ $entidad }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}" style="padding: 8px 15px; border-radius: 5px; background: var(--bg); border: 1px solid var(--line); color: var(--text);">
        </div>
        <div>
            <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}" style="padding: 8px 15px; border-radius: 5px; background: var(--bg); border: 1px solid var(--line); color: var(--text);">
        </div>
        <button type="submit" class="pill ok cursor-pointer" style="border: none;">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        <a href="{{ route('auditorias.index') }}" class="pill cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
            <i class="fas fa-eraser"></i> Limpiar
        </a>
    </form>
</section>

<section class="panel table-panel">
    <div class="panel-head mb-4">
        <h2>Registro de acciones</h2>
        <span class="hint">{{ $auditorias->total() }} evento(s)</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-sm">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Accion</th>
                    <th>Entidad</th>
                    <th>Datos nuevos</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditorias as $auditoria)
                    <tr>
                        <td class="mono text-muted">{{ $auditoria->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $auditoria->usuario->name ?? 'Sistema' }}</td>
                        <td><span class="pill pending">{{ $auditoria->accion }}</span></td>
                        <td class="mono">{{ $auditoria->entidad_tipo ?? '-' }} #{{ $auditoria->entidad_id ?? '-' }}</td>
                        <td style="max-width: 360px;">
                            <details>
                                <summary style="cursor: pointer; color: var(--primary);">Ver detalle</summary>
                                <pre style="white-space: pre-wrap; color: var(--muted); font-size: 11px; margin-top: 8px;">{{ json_encode($auditoria->valores_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                        </td>
                        <td class="mono text-muted">{{ $auditoria->ip }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 20px;">
                            No hay eventos de auditoria registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 18px;">
        {{ $auditorias->links() }}
    </div>
</section>

<style>
    .table-sm th, .table-sm td {
        padding: 8px 10px;
        vertical-align: top;
    }
</style>
@endsection

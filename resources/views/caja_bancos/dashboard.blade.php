@extends('layouts.app')

@section('title', 'Caja y Bancos - PISFIL SIG')
@section('header_title', 'Tesorería: Caja y Bancos')

@section('content')
<!-- KPIs -->
<section class="kpi-grid stagger-1 mb-8" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
    <div class="kpi-card" style="border-color: rgba(37,99,235,0.3);">
        <span class="kpi-label" style="color: var(--primary);">Fondo Total Soles (S/)</span>
        <span class="kpi-value" style="color: var(--primary);">{{ number_format($totalSoles, 2) }}</span>
        <span class="kpi-delta up"><i class="fas fa-coins"></i> Saldo consolidado</span>
    </div>
    <div class="kpi-card" style="border-color: rgba(79,174,122,0.3);">
        <span class="kpi-label" style="color: var(--success);">Fondo Total Dólares ($)</span>
        <span class="kpi-value" style="color: var(--success);">{{ number_format($totalDolares, 2) }}</span>
        <span class="kpi-delta up"><i class="fas fa-money-bill-wave"></i> Saldo consolidado</span>
    </div>
</section>

@if($errors->any())
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); color: var(--danger);">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if(session('success'))
    <div style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: rgba(79, 174, 122, 0.1); border: 1px solid rgba(79, 174, 122, 0.3); color: var(--success);">
        {{ session('success') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;" class="stagger-2">
    <!-- Izquierda: Cuentas -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        <section class="panel table-panel">
            <span class="panel-tag">Cuentas</span>
            <div class="panel-head mb-4">
                <h2>Cuentas y Cajas Registradas</h2>
            </div>
            
            <div style="display: grid; gap: 15px;">
                @forelse($cuentas as $cuenta)
                    <a href="{{ route('caja-bancos.show-cuenta', $cuenta) }}" class="kpi-card hover:opacity-80" style="text-decoration: none; display: flex; justify-content: space-between; align-items: center; padding: 15px; {{ $cuenta->tipo === 'caja' ? 'border-left: 4px solid var(--secondary);' : 'border-left: 4px solid var(--primary);' }}">
                        <div>
                            <div style="font-weight: bold; color: var(--text); font-size: 16px;">
                                @if($cuenta->tipo === 'caja')
                                    <i class="fas fa-cash-register mr-2 text-muted"></i>
                                @else
                                    <i class="fas fa-building-columns mr-2 text-muted"></i>
                                @endif
                                {{ $cuenta->nombre }}
                            </div>
                            <div style="font-size: 12px; color: var(--muted); margin-top: 5px;">
                                {{ $cuenta->tipo === 'banco' ? $cuenta->banco . ' | ' . $cuenta->numero_cuenta : 'Caja Fuerte / Chica' }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 18px; font-weight: bold; color: {{ $cuenta->saldo_actual >= 0 ? 'var(--success)' : 'var(--danger)' }};" class="mono">
                                {{ $cuenta->moneda === 'PEN' ? 'S/' : '$' }} {{ number_format($cuenta->saldo_actual, 2) }}
                            </div>
                            <div style="font-size: 11px; color: var(--muted);">{{ $cuenta->transacciones_count }} Movimientos</div>
                        </div>
                    </a>
                @empty
                    <div style="text-align: center; color: var(--muted); padding: 20px;">
                        No hay cuentas creadas. Utiliza el formulario para añadir una caja o banco.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Crear Cuenta -->
        <section class="panel">
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px;">Crear Nueva Cuenta</h2>
            </div>
            <form action="{{ route('caja-bancos.store-cuenta') }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Tipo</label>
                        <select name="tipo" id="tipoSelect" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                            <option value="banco">Cuenta Bancaria</option>
                            <option value="caja">Caja Chica / Efectivo</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Nombre Identificador</label>
                        <input type="text" name="nombre" required placeholder="Ej: BCP Principal" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>

                <div id="bancoFields" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Banco</label>
                        <input type="text" name="banco" placeholder="Ej: BCP" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Número de Cuenta</label>
                        <input type="text" name="numero_cuenta" placeholder="193-..." style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Moneda</label>
                        <select name="moneda" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                            <option value="PEN">Soles (PEN)</option>
                            <option value="USD">Dólares (USD)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Saldo Inicial</label>
                        <input type="number" step="0.01" min="0" name="saldo_actual" value="0.00" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>

                <button type="submit" class="pill ok cursor-pointer" style="border: none; justify-content: center; margin-top: 10px;">
                    <i class="fas fa-save mr-1"></i> Guardar Cuenta
                </button>
            </form>
            
            <script>
                document.getElementById('tipoSelect').addEventListener('change', function() {
                    const bancoFields = document.getElementById('bancoFields');
                    if (this.value === 'caja') {
                        bancoFields.style.display = 'none';
                    } else {
                        bancoFields.style.display = 'grid';
                    }
                });
            </script>
        </section>
    </div>

    <!-- Derecha: Movimientos Recientes -->
    <section class="panel table-panel">
        <span class="panel-tag">Últimos Movimientos</span>
        <div class="panel-head mb-4">
            <h2>Registro General (Todas las cuentas)</h2>
        </div>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cuenta</th>
                        <th>Detalle</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimasTransacciones as $tx)
                        <tr>
                            <td class="mono" style="font-size: 12px; color: var(--muted);">{{ $tx->fecha_transaccion->format('d/m/Y') }}</td>
                            <td>
                                <div style="font-weight: 500;">{{ $tx->cuenta->nombre }}</div>
                            </td>
                            <td>
                                <div style="font-size: 13px;">{{ $tx->motivo }}</div>
                                <div style="font-size: 11px; color: var(--muted);">Usuario: {{ $tx->usuarioRegistra->name }}</div>
                            </td>
                            <td style="text-align: right; font-weight: bold; font-family: var(--font-mono); font-size: 14px;">
                                @if($tx->tipo === 'ingreso')
                                    <span style="color: var(--success);">+ {{ number_format($tx->monto, 2) }}</span>
                                @elseif($tx->tipo === 'egreso')
                                    <span style="color: var(--danger);">- {{ number_format($tx->monto, 2) }}</span>
                                @else
                                    <span style="color: var(--primary);"><i class="fas fa-exchange-alt"></i> {{ number_format($tx->monto, 2) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--muted); padding: 20px;">
                                No hay movimientos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Estado de Cuenta: ' . $cuenta->nombre . ' - PISFIL SIG')
@section('header_title', 'Estado de Cuenta')

@section('content')
<div class="panel-head mb-4" style="display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('caja-bancos.dashboard') }}" class="pill hover:opacity-80 cursor-pointer text-decoration-none" style="border: 1px solid var(--line); color: var(--text);">
        <i class="fas fa-arrow-left"></i> Volver a Tesorería
    </a>
</div>

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

<div class="kpi-grid stagger-1 mb-8" style="grid-template-columns: 2fr 1fr;">
    <div class="kpi-card" style="border-left: 4px solid var(--primary);">
        <span class="kpi-label" style="font-size: 14px;">Detalles de la Cuenta</span>
        <div style="margin-top: 10px;">
            <div style="font-size: 24px; font-weight: bold; color: var(--text);">
                {{ $cuenta->nombre }}
            </div>
            <div style="font-size: 14px; color: var(--muted); margin-top: 5px;">
                @if($cuenta->tipo === 'banco')
                    Banco: {{ $cuenta->banco }} | Nro: {{ $cuenta->numero_cuenta }}
                @else
                    Tipo: Caja Interna
                @endif
                | Moneda: <b>{{ $cuenta->moneda }}</b>
            </div>
        </div>
    </div>
    
    <div class="kpi-card" style="border-left: 4px solid {{ $cuenta->saldo_actual >= 0 ? 'var(--success)' : 'var(--danger)' }}; text-align: right;">
        <span class="kpi-label" style="font-size: 14px;">Saldo Actual Disponible</span>
        <span class="kpi-value mono" style="font-size: 32px; color: {{ $cuenta->saldo_actual >= 0 ? 'var(--success)' : 'var(--danger)' }};">
            {{ $cuenta->moneda === 'PEN' ? 'S/' : '$' }} {{ number_format($cuenta->saldo_actual, 2) }}
        </span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;" class="stagger-2">
    <!-- Libro Mayor -->
    <section class="panel table-panel">
        <span class="panel-tag">Movimientos</span>
        <div class="panel-head mb-4">
            <h2>Libro Mayor (Ingresos y Egresos)</h2>
        </div>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Detalle</th>
                        <th>Ingreso</th>
                        <th>Egreso</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transacciones as $tx)
                        <tr>
                            <td class="mono" style="font-size: 13px; color: var(--muted);">{{ $tx->fecha_transaccion->format('d/m/Y') }}</td>
                            <td>
                                @if($tx->tipo === 'ingreso')
                                    <span class="pill ok" style="padding: 2px 8px; font-size: 11px;">Ingreso</span>
                                @elseif($tx->tipo === 'egreso')
                                    <span class="pill danger" style="padding: 2px 8px; font-size: 11px;">Egreso</span>
                                @else
                                    <span class="pill pending" style="padding: 2px 8px; font-size: 11px; background: rgba(37,99,235,0.1); color: var(--primary);">Transferencia</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 13px;">{{ $tx->motivo }}</div>
                                @if($tx->referencia)
                                    <div style="font-size: 11px; color: var(--muted); font-family: var(--font-mono);">Ref: {{ $tx->referencia }}</div>
                                @endif
                                @if($tx->cuentaContable)
                                    <div style="margin-top: 4px; font-size: 11px; color: var(--primary);">
                                        <i class="fas fa-book"></i> PCGE: {{ $tx->cuentaContable->codigo }} - {{ $tx->cuentaContable->descripcion }}
                                    </div>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: bold; font-family: var(--font-mono); color: var(--success); font-size: 14px;">
                                {{ ($tx->tipo === 'ingreso' || ($tx->tipo === 'transferencia' && str_contains($tx->motivo, 'ENTRANTE'))) ? number_format($tx->monto, 2) : '' }}
                            </td>
                            <td style="text-align: right; font-weight: bold; font-family: var(--font-mono); color: var(--danger); font-size: 14px;">
                                {{ ($tx->tipo === 'egreso' || ($tx->tipo === 'transferencia' && str_contains($tx->motivo, 'SALIENTE'))) ? number_format($tx->monto, 2) : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--muted); padding: 20px;">
                                No hay transacciones registradas en esta cuenta.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">
            {{ $transacciones->links('pagination::tailwind') }}
        </div>
    </section>

    <!-- Formularios de Acción -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Registrar Movimiento Manual -->
        <section class="panel">
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px;">Registrar Operación</h2>
            </div>
            <form action="{{ route('caja-bancos.movimiento', $cuenta) }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; padding: 10px; border: 1px solid rgba(79, 174, 122, 0.3); border-radius: 8px; background: rgba(79, 174, 122, 0.05); color: var(--success);">
                        <input type="radio" name="tipo" value="ingreso" required checked style="accent-color: var(--success);">
                        Ingreso (+)
                    </label>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; padding: 10px; border: 1px solid rgba(220, 38, 38, 0.3); border-radius: 8px; background: rgba(220, 38, 38, 0.05); color: var(--danger);">
                        <input type="radio" name="tipo" value="egreso" required style="accent-color: var(--danger);">
                        Egreso (-)
                    </label>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Monto ({{ $cuenta->moneda }})</label>
                    <input type="number" name="monto" step="0.01" min="0.01" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text); font-family: var(--font-mono);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Motivo / Descripción</label>
                    <input type="text" name="motivo" required placeholder="Ej: Pago de servicios de luz" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Asiento Contable (PCGE) - Opcional</label>
                    <select name="cuenta_contable_id" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                        <option value="">-- No Asociar --</option>
                        @if(isset($cuentasContables))
                            @foreach($cuentasContables as $cc)
                                <option value="{{ $cc->id }}">{{ $cc->codigo }} - {{ $cc->descripcion }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Referencia / N° Op</label>
                        <input type="text" name="referencia" pattern="[A-Za-z0-9 ._#/-]+" maxlength="100" title="Use solo letras, numeros, espacios, punto, guion, barra o numeral" placeholder="Opcional" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Fecha</label>
                        <input type="date" name="fecha_transaccion" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                    </div>
                </div>

                <button type="submit" class="pill ok cursor-pointer" style="border: none; justify-content: center; margin-top: 10px;">
                    <i class="fas fa-save mr-1"></i> Guardar Movimiento
                </button>
            </form>
        </section>

        <!-- Transferencia entre Cuentas -->
        @if($todasCuentas->count() > 0)
        <section class="panel" style="border-color: rgba(37,99,235,0.3);">
            <div class="panel-head mb-4">
                <h2 style="font-size: 16px; color: var(--primary);">Transferir Dinero a otra Cuenta</h2>
            </div>
            <form action="{{ route('caja-bancos.transferencia', $cuenta) }}" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                @csrf
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Cuenta Destino (Debe ser la misma moneda)</label>
                    <select name="cuenta_destino_id" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                        <option value="">-- Seleccionar Destino --</option>
                        @foreach($todasCuentas as $destino)
                            <option value="{{ $destino->id }}">{{ $destino->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Monto a transferir</label>
                    <input type="number" name="monto" step="0.01" min="0.01" max="{{ $cuenta->saldo_actual }}" required style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text); font-family: var(--font-mono);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px;">Motivo</label>
                    <input type="text" name="motivo" required placeholder="Ej: Reposición de caja" style="width: 100%; padding: 8px; border-radius: 5px; background: var(--surface-2); border: 1px solid var(--line); color: var(--text);">
                </div>
                <input type="hidden" name="fecha_transaccion" value="{{ date('Y-m-d') }}">

                <button type="submit" class="pill pending cursor-pointer" style="border: none; justify-content: center; background: rgba(37,99,235,0.1); color: var(--primary);">
                    <i class="fas fa-exchange-alt mr-1"></i> Confirmar Transferencia
                </button>
            </form>
        </section>
        @endif
    </div>
</div>
@endsection

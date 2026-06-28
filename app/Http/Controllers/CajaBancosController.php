<?php

namespace App\Http\Controllers;

use App\Models\CuentaFinanciera;
use App\Models\TransaccionFinanciera;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CajaBancosController extends Controller
{
    public function dashboard(): View
    {
        $cuentas = CuentaFinanciera::withCount('transacciones')->get();
        $totalSoles = $cuentas->where('moneda', 'PEN')->sum('saldo_actual');
        $totalDolares = $cuentas->where('moneda', 'USD')->sum('saldo_actual');

        $ultimasTransacciones = TransaccionFinanciera::with('cuenta', 'usuarioRegistra')
            ->orderByDesc('fecha_transaccion')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('caja_bancos.dashboard', compact('cuentas', 'totalSoles', 'totalDolares', 'ultimasTransacciones'));
    }

    public function storeCuenta(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:caja,banco',
            'banco' => 'nullable|string|max:50',
            'numero_cuenta' => 'nullable|string|max:50',
            'moneda' => 'required|in:PEN,USD',
            'saldo_actual' => 'required|numeric|min:0'
        ]);

        CuentaFinanciera::create($validated);

        return redirect()->route('caja-bancos.dashboard')->with('success', 'Cuenta registrada exitosamente.');
    }

    public function showCuenta(CuentaFinanciera $cuenta): View
    {
        $transacciones = $cuenta->transacciones()
            ->with('usuarioRegistra', 'cuentaDestino', 'cuentaContable')
            ->orderByDesc('fecha_transaccion')
            ->orderByDesc('created_at')
            ->paginate(20);

        $todasCuentas = CuentaFinanciera::where('id', '!=', $cuenta->id)
            ->where('moneda', $cuenta->moneda)
            ->where('estado', true)
            ->get();

        $cuentasContables = \App\Models\CuentaContable::orderBy('codigo')->get();

        return view('caja_bancos.show', compact('cuenta', 'transacciones', 'todasCuentas', 'cuentasContables'));
    }

    public function registrarMovimiento(Request $request, CuentaFinanciera $cuenta): RedirectResponse
    {
        $validated = $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'monto' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:100',
            'fecha_transaccion' => 'required|date',
            'cuenta_contable_id' => 'nullable|exists:cuentas_contables,id',
        ]);

        try {
            DB::beginTransaction();

            if ($validated['tipo'] === 'egreso' && $cuenta->saldo_actual < $validated['monto']) {
                throw new \Exception('Saldo insuficiente para realizar este egreso.');
            }

            $transaccion = new TransaccionFinanciera($validated);
            $transaccion->cuenta_financiera_id = $cuenta->id;
            $transaccion->usuario_registra_id = Auth::id();
            $transaccion->save();

            if ($validated['tipo'] === 'ingreso') {
                $cuenta->increment('saldo_actual', $validated['monto']);
            } else {
                $cuenta->decrement('saldo_actual', $validated['monto']);
            }

            DB::commit();
            return back()->with('success', 'Movimiento registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function registrarTransferencia(Request $request, CuentaFinanciera $cuentaOrigen): RedirectResponse
    {
        $validated = $request->validate([
            'cuenta_destino_id' => 'required|exists:cuentas_financieras,id|different:' . $cuentaOrigen->id,
            'monto' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
            'fecha_transaccion' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $cuentaDestino = CuentaFinanciera::findOrFail($validated['cuenta_destino_id']);

            if ($cuentaOrigen->moneda !== $cuentaDestino->moneda) {
                throw new \Exception('No se puede transferir entre cuentas de distinta moneda (aún no se soporta tipo de cambio).');
            }

            if ($cuentaOrigen->saldo_actual < $validated['monto']) {
                throw new \Exception('Saldo insuficiente en la cuenta de origen.');
            }

            // 1. Egreso de la cuenta origen
            TransaccionFinanciera::create([
                'cuenta_financiera_id' => $cuentaOrigen->id,
                'cuenta_destino_id' => $cuentaDestino->id,
                'tipo' => 'transferencia',
                'monto' => $validated['monto'],
                'motivo' => 'TRANSFERENCIA SALIENTE: ' . $validated['motivo'],
                'fecha_transaccion' => $validated['fecha_transaccion'],
                'usuario_registra_id' => Auth::id(),
            ]);
            $cuentaOrigen->decrement('saldo_actual', $validated['monto']);

            // 2. Ingreso a la cuenta destino
            TransaccionFinanciera::create([
                'cuenta_financiera_id' => $cuentaDestino->id,
                'tipo' => 'ingreso',
                'monto' => $validated['monto'],
                'motivo' => 'TRANSFERENCIA ENTRANTE DE ' . $cuentaOrigen->nombre . ': ' . $validated['motivo'],
                'fecha_transaccion' => $validated['fecha_transaccion'],
                'usuario_registra_id' => Auth::id(),
            ]);
            $cuentaDestino->increment('saldo_actual', $validated['monto']);

            DB::commit();
            return back()->with('success', 'Transferencia completada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

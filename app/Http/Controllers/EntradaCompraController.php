<?php

namespace App\Http\Controllers;

use App\Models\EntradaCompra;
use App\Models\DetalleEntradaCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Kardex;
use App\Services\KardexService;
use App\Services\AsientoContableService;
use App\Models\CuentaFinanciera;
use App\Models\TransaccionFinanciera;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class EntradaCompraController extends Controller
{
    /**
     * Mostrar lista de entradas de compra
     */
    public function index(): View
    {
        $entradas = EntradaCompra::with('proveedor', 'usuario')
            ->orderByDesc('fecha_emision')
            ->paginate(15);

        return view('entradas_compra.index', compact('entradas'));
    }

    /**
     * Mostrar formulario para crear entrada
     */
    public function create(): View
    {
        $proveedores = Proveedor::where('estado', true)->get();

        return view('entradas_compra.create', compact('proveedores'));
    }

    /**
     * Guardar nueva entrada
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero_documento' => 'required|string|max:50|unique:entradas_compra',
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_emision' => 'required|date',
        ]);

        $validated['usuario_id'] = Auth::id();
        $validated['estado'] = 'pendiente';

        EntradaCompra::create($validated);

        return redirect()->route('entradas-compra.index')
            ->with('success', 'Entrada de compra creada exitosamente');
    }

    /**
     * Mostrar detalles de la entrada
     */
    public function show(EntradaCompra $entradaCompra): View
    {
        $entradaCompra->load('detalles.producto', 'proveedor', 'usuario');
        $cuentasFinancieras = \App\Models\CuentaFinanciera::where('estado', true)->get();
        $cuentasContables = \App\Models\CuentaContable::orderBy('codigo')->get();

        return view('entradas_compra.show', compact('entradaCompra', 'cuentasFinancieras', 'cuentasContables'));
    }

    /**
     * Mostrar formulario para editar entrada
     */
    public function edit(EntradaCompra $entradaCompra): View
    {
        $proveedores = Proveedor::where('estado', true)->get();

        return view('entradas_compra.edit', compact('entradaCompra', 'proveedores'));
    }

    /**
     * Actualizar entrada
     */
    public function update(Request $request, EntradaCompra $entradaCompra): RedirectResponse
    {
        $validated = $request->validate([
            'numero_documento' => 'required|string|max:50|unique:entradas_compra,numero_documento,' . $entradaCompra->id,
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:pendiente,recibida,validada,rechazada',
        ]);

        $entradaCompra->update($validated);

        return redirect()->route('entradas-compra.index')
            ->with('success', 'Entrada actualizada exitosamente');
    }

    /**
     * Cambiar estado de la entrada
     */
    public function cambiarEstado(Request $request, EntradaCompra $entradaCompra, KardexService $kardexService, AsientoContableService $asientoService): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => 'required|in:pendiente,recibida,validada,rechazada',
        ]);

        try {
            DB::beginTransaction();

            $datosActualizacion = ['estado' => $validated['estado']];

            if ($validated['estado'] === EntradaCompra::ESTADO_VALIDADA && $entradaCompra->estado !== EntradaCompra::ESTADO_VALIDADA) {
                if ($entradaCompra->detalles()->count() === 0) {
                    DB::rollBack();
                    return back()->withErrors(['error' => 'No se puede validar una compra sin detalles (productos).']);
                }

                foreach ($entradaCompra->detalles as $detalle) {
                    $kardexService->registrarMovimiento(
                        $detalle->producto_id,
                        Kardex::TIPO_ENTRADA,
                        $detalle->cantidad_solicitada,
                        $detalle->precio_unitario,
                        Auth::id(),
                        'EntradaCompra',
                        $entradaCompra->id,
                        'Recepción de compra N° ' . $entradaCompra->numero_documento
                    );
                }

                $datosActualizacion['fecha_recepcion'] = now();
                $asientoService->registrarCompra($entradaCompra->fresh(['proveedor', 'detalles']), Auth::id());
            }

            $entradaCompra->update($datosActualizacion);

            DB::commit();
            return back()->with('success', 'Estado actualizado y Kárdex procesado (si aplica).');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar el Kárdex: ' . $e->getMessage()]);
        }
    }

    /**
     * Registrar un pago a la cuenta por pagar
     */
    public function registrarPago(Request $request, EntradaCompra $entradaCompra, AsientoContableService $asientoService): RedirectResponse
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'cuenta_financiera_id' => 'required|exists:cuentas_financieras,id',
            'cuenta_contable_id' => 'nullable|exists:cuentas_contables,id',
        ]);

        try {
            DB::beginTransaction();

            $totalFactura = $entradaCompra->detalles()->sum('costo_total');
            $nuevoMontoPagado = $entradaCompra->monto_pagado + $validated['monto'];

            if ($nuevoMontoPagado > $totalFactura) {
                throw new Exception('El monto de pago excede la deuda total de la factura.');
            }

            $cuenta = CuentaFinanciera::findOrFail($validated['cuenta_financiera_id']);
            if ($cuenta->saldo_actual < $validated['monto']) {
                throw new Exception('Saldo insuficiente en la cuenta/caja seleccionada.');
            }

            $estadoPago = EntradaCompra::PAGO_PARCIAL;
            if (abs($totalFactura - $nuevoMontoPagado) < 0.01) {
                $estadoPago = EntradaCompra::PAGO_PAGADO;
            }

            $entradaCompra->update([
                'monto_pagado' => $nuevoMontoPagado,
                'estado_pago' => $estadoPago
            ]);

            $transaccion = TransaccionFinanciera::create([
                'cuenta_financiera_id' => $cuenta->id,
                'tipo' => 'egreso',
                'monto' => $validated['monto'],
                'motivo' => 'PAGO COMPRA: ' . $entradaCompra->numero_documento . ' (Proveedor: ' . $entradaCompra->proveedor->nombre_empresa . ')',
                'referencia' => 'C-' . $entradaCompra->id,
                'fecha_transaccion' => now(),
                'usuario_registra_id' => Auth::id(),
                'cuenta_contable_id' => $validated['cuenta_contable_id'] ?? null,
            ]);

            $cuenta->decrement('saldo_actual', $validated['monto']);
            $asientoService->registrarPagoCompra($entradaCompra->fresh('proveedor'), $transaccion);

            DB::commit();
            return back()->with('success', 'Pago registrado exitosamente. Se ha descontado el saldo de tesorería.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el pago: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar entrada
     */
    public function destroy(EntradaCompra $entradaCompra): RedirectResponse
    {
        $entradaCompra->delete();

        return redirect()->route('entradas-compra.index')
            ->with('success', 'Entrada eliminada exitosamente');
    }

    /**
     * Agregar detalle a entrada (AJAX)
     */
    public function agregarDetalle(Request $request, EntradaCompra $entradaCompra)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_solicitada' => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);
        $costo_total = $validated['cantidad_solicitada'] * $validated['precio_unitario'];

        $detalle = DetalleEntradaCompra::create([
            'entrada_compra_id' => $entradaCompra->id,
            'producto_id' => $validated['producto_id'],
            'cantidad_solicitada' => $validated['cantidad_solicitada'],
            'precio_unitario' => $validated['precio_unitario'],
            'costo_total' => $costo_total,
        ]);

        return response()->json([
            'success' => true,
            'detalle' => $detalle->load('producto'),
        ]);
    }
}

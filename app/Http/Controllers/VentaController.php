<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CuentaFinanciera;
use App\Models\TransaccionFinanciera;
use App\Models\Kardex;
use App\Services\KardexService;
use App\Services\AsientoContableService;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function index(): View
    {
        $ventas = Venta::with('cliente', 'cuentaFinanciera')->orderByDesc('created_at')->paginate(20);
        return view('ventas.index', compact('ventas'));
    }

    /** Mapa de series por tipo de comprobante */
    private const SERIES = [
        'Factura' => 'F001',
        'Boleta'  => 'B001',
        'Ticket'  => 'T001',
    ];

    /**
     * Devuelve el siguiente correlativo disponible para un tipo de comprobante.
     * Usa MAX + 1 para no depender de auto-increment y ser seguro ante anulaciones.
     */
    private function siguienteCorrelativo(string $tipo): int
    {
        $serie = self::SERIES[$tipo] ?? 'X001';
        $max = Venta::where('tipo_comprobante', $tipo)
            ->where('serie_comprobante', $serie)
            ->max(DB::raw('CAST(numero_comprobante AS UNSIGNED)'));

        return ($max ?? 0) + 1;
    }

    public function create(): View
    {
        $clientes = Cliente::where('estado', true)->orderBy('nombre')->get();
        $productos = Producto::where('estado', 'activo')->get();
        $cuentas = CuentaFinanciera::where('estado', true)->get();
        $cuentasContables = \App\Models\CuentaContable::orderBy('codigo')->get();

        // Pre-calcular siguiente correlativo para cada tipo (para mostrarlo en el form)
        $correlativos = [
            'Factura' => $this->siguienteCorrelativo('Factura'),
            'Boleta'  => $this->siguienteCorrelativo('Boleta'),
            'Ticket'  => $this->siguienteCorrelativo('Ticket'),
        ];
        $series = self::SERIES;

        return view('ventas.create', compact('clientes', 'productos', 'cuentas', 'cuentasContables', 'correlativos', 'series'));
    }

    public function store(Request $request, KardexService $kardexService, AsientoContableService $asientoService): RedirectResponse
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_comprobante' => 'required|in:Factura,Boleta,Ticket',
            'serie_comprobante' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z0-9-]+$/'],
            'numero_comprobante' => ['nullable', 'digits_between:1,20'],
            'fecha_venta' => 'required|date',
            'moneda' => 'required|in:PEN,USD',
            'condicion_pago' => 'required|in:contado,credito',
            'cuenta_financiera_id' => 'nullable|required_if:condicion_pago,contado|exists:cuentas_financieras,id',
            'cuenta_contable_id' => 'nullable|exists:cuentas_contables,id',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|exists:productos,id',
            'cantidades' => 'required|array|min:1',
            'cantidades.*' => 'required|numeric|min:0.01',
            'precios' => 'required|array|min:1',
            'precios.*' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generar serie y correlativo de forma atómica (bloquea la tabla para evitar duplicados)
            Venta::lockForUpdate()->max('id'); // adquirir lock
            $tipoComprobante  = $request->tipo_comprobante;
            $serieAuto        = self::SERIES[$tipoComprobante] ?? 'X001';
            $correlativoAuto  = $this->siguienteCorrelativo($tipoComprobante);

            $total = 0;
            $detallesToInsert = [];

            // 1. Crear cabecera de la venta en estado BORRADOR
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'tipo_comprobante' => $tipoComprobante,
                'serie_comprobante' => $serieAuto,
                'numero_comprobante' => $correlativoAuto,
                'fecha_venta' => $request->fecha_venta,
                'moneda' => $request->moneda,
                'total' => 0,
                'condicion_pago' => $request->condicion_pago,
                'estado' => 'borrador',
                'estado_pago' => 'pendiente',
                'monto_cobrado' => 0,
                'cuenta_financiera_id' => $request->cuenta_financiera_id,
                'usuario_registra_id' => Auth::id(),
            ]);

            // 2. Procesar detalles
            foreach ($request->productos as $index => $producto_id) {
                $cantidad = $request->cantidades[$index];
                $precio = $request->precios[$index];
                
                if ($cantidad <= 0 || $precio < 0) continue;

                $subtotal = $cantidad * $precio;
                $total += $subtotal;

                // Crear el detalle
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal
                ]);

                // 3. Registrar SALIDA del Kárdex (Automatización)
                $kardexService->registrarMovimiento(
                    $producto_id,
                    Kardex::TIPO_SALIDA,
                    $cantidad,
                    0,
                    Auth::id(),
                    'Venta',
                    $venta->id,
                    'Salida por ' . $request->tipo_comprobante . ' ' . $request->serie_comprobante . '-' . $request->numero_comprobante
                );
            }

            if ($total <= 0) {
                throw new \Exception('El total de la venta debe ser mayor a 0.');
            }

            // 4. Actualizar total y estados
            $estadoPago = ($request->condicion_pago === 'contado') ? 'pagado' : 'pendiente';
            $montoCobrado = ($request->condicion_pago === 'contado') ? $total : 0;

            $venta->update([
                'total' => $total,
                'estado' => 'pagada', // Documento procesado (el estado de deuda está en estado_pago)
                'estado_pago' => $estadoPago,
                'monto_cobrado' => $montoCobrado
            ]);

            // 5. Generar INGRESO de dinero solo si es al contado
            if ($request->condicion_pago === 'contado') {
                $cuenta = CuentaFinanciera::findOrFail($request->cuenta_financiera_id);
                if ($cuenta->moneda !== $venta->moneda) {
                    throw new \Exception("La cuenta destino debe tener la misma moneda que la venta ({$venta->moneda}).");
                }

                TransaccionFinanciera::create([
                    'cuenta_financiera_id' => $cuenta->id,
                    'tipo' => 'ingreso',
                    'monto' => $total,
                    'motivo' => 'COBRO VENTA: ' . $venta->tipo_comprobante . ' ' . $venta->serie_comprobante . '-' . $venta->numero_comprobante,
                    'referencia' => 'V-' . $venta->id,
                    'fecha_transaccion' => $venta->fecha_venta,
                    'usuario_registra_id' => Auth::id(),
                    'cuenta_contable_id' => $request->cuenta_contable_id,
                ]);
                $cuenta->increment('saldo_actual', $total);
            }

            $ventaActualizada = $venta->fresh(['cliente', 'cuentaFinanciera']);
            $asientoService->registrarVenta($ventaActualizada, Auth::id());
            $asientoService->registrarCostoVenta($ventaActualizada, Auth::id());
            AuditoriaService::registrar('venta.creada', $ventaActualizada, null, [
                'venta' => $ventaActualizada->toArray(),
                'total' => $total,
            ]);

            DB::commit();

            $msg = $request->condicion_pago === 'contado' 
                ? 'Venta al contado registrada. El inventario ha sido descontado y el cobro ha ingresado a la caja.' 
                : 'Venta al crédito registrada. El inventario ha sido descontado, generándose una cuenta por cobrar.';

            return redirect()->route('ventas.show', $venta)->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()]);
        }
    }

    public function show(Venta $venta): View
    {
        $venta->load(['cliente', 'detalles.producto', 'cuentaFinanciera', 'usuarioRegistra']);
        $cuentasFinancieras = \App\Models\CuentaFinanciera::where('estado', true)
            ->where('moneda', $venta->moneda)
            ->get();
        $cuentasContables = \App\Models\CuentaContable::orderBy('codigo')->get();

        return view('ventas.show', compact('venta', 'cuentasFinancieras', 'cuentasContables'));
    }

    public function registrarCobro(Request $request, Venta $venta, AsientoContableService $asientoService): RedirectResponse
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'cuenta_financiera_id' => 'required|exists:cuentas_financieras,id',
            'cuenta_contable_id' => 'nullable|exists:cuentas_contables,id',
        ]);

        try {
            DB::beginTransaction();

            $totalVenta = $venta->total;
            $nuevoMontoCobrado = $venta->monto_cobrado + $validated['monto'];

            if ($nuevoMontoCobrado > $totalVenta) {
                throw new \Exception('El monto de cobro excede la deuda total de la venta.');
            }

            $estadoPago = 'parcial';
            if (abs($totalVenta - $nuevoMontoCobrado) < 0.01) {
                $estadoPago = 'pagado';
            }

            $venta->update([
                'monto_cobrado' => $nuevoMontoCobrado,
                'estado_pago' => $estadoPago
            ]);

            $cuenta = CuentaFinanciera::findOrFail($validated['cuenta_financiera_id']);
            if ($cuenta->moneda !== $venta->moneda) {
                throw new \Exception("La cuenta destino debe tener la misma moneda que la venta ({$venta->moneda}).");
            }

            $transaccion = TransaccionFinanciera::create([
                'cuenta_financiera_id' => $cuenta->id,
                'tipo' => 'ingreso',
                'monto' => $validated['monto'],
                'motivo' => 'COBRO CUOTA VENTA: ' . $venta->tipo_comprobante . ' ' . $venta->serie_comprobante . '-' . $venta->numero_comprobante . ' (Cliente: ' . $venta->cliente->nombre . ')',
                'referencia' => 'V-' . $venta->id,
                'fecha_transaccion' => now(),
                'usuario_registra_id' => Auth::id(),
                'cuenta_contable_id' => $validated['cuenta_contable_id'] ?? null,
            ]);

            $cuenta->increment('saldo_actual', $validated['monto']);
            $asientoService->registrarCobroVenta($venta->fresh('cliente'), $transaccion);
            AuditoriaService::registrar('venta.cobro_registrado', $venta, null, [
                'monto' => $validated['monto'],
                'transaccion_id' => $transaccion->id,
                'estado_pago' => $estadoPago,
            ]);

            DB::commit();
            return back()->with('success', 'Cobro registrado exitosamente. Se ha ingresado el dinero a tesorería.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar el cobro: ' . $e->getMessage()]);
        }
    }
}

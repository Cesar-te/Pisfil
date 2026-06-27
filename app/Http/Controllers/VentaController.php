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

    public function create(): View
    {
        $clientes = Cliente::where('estado', true)->orderBy('nombre')->get();
        // Solo productos terminados o disponibles para la venta. Asumiremos todos por ahora.
        $productos = Producto::where('estado', true)->get();
        $cuentas = CuentaFinanciera::where('estado', true)->get();

        return view('ventas.create', compact('clientes', 'productos', 'cuentas'));
    }

    public function store(Request $request, KardexService $kardexService): RedirectResponse
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_comprobante' => 'required|in:Factura,Boleta,Ticket',
            'serie_comprobante' => 'nullable|string|max:10',
            'numero_comprobante' => 'nullable|string|max:20',
            'fecha_venta' => 'required|date',
            'moneda' => 'required|in:PEN,USD',
            'cuenta_financiera_id' => 'required|exists:cuentas_financieras,id',
            'productos' => 'required|array|min:1',
            'productos.*' => 'exists:productos,id',
            'cantidades' => 'required|array|min:1',
            'precios' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $detallesToInsert = [];

            // 1. Crear cabecera de la venta en estado BORRADOR
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'tipo_comprobante' => $request->tipo_comprobante,
                'serie_comprobante' => $request->serie_comprobante,
                'numero_comprobante' => $request->numero_comprobante,
                'fecha_venta' => $request->fecha_venta,
                'moneda' => $request->moneda,
                'total' => 0,
                'estado' => 'borrador',
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
                // Se registra a precio 0 para que el KardexService le asigne su Costo Promedio. 
                // El Kárdex calculará el costo de salida. Si no hay stock, esto lanzará excepción.
                $kardexService->registrarMovimiento(
                    $producto_id,
                    Kardex::TIPO_SALIDA,
                    $cantidad,
                    0, // El servicio de kárdex costea automáticamente
                    Auth::id(),
                    'Venta',
                    $venta->id,
                    'Salida por ' . $request->tipo_comprobante . ' ' . $request->serie_comprobante . '-' . $request->numero_comprobante
                );
            }

            if ($total <= 0) {
                throw new \Exception('El total de la venta debe ser mayor a 0.');
            }

            // 4. Actualizar total y estado a PAGADA
            $venta->update([
                'total' => $total,
                'estado' => 'pagada'
            ]);

            // 5. Generar INGRESO de dinero en Caja/Banco (Automatización)
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
            ]);
            $cuenta->increment('saldo_actual', $total);

            DB::commit();

            return redirect()->route('ventas.show', $venta)->with('success', 'Venta registrada exitosamente. El inventario ha sido descontado y el cobro ha ingresado a la caja.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()]);
        }
    }

    public function show(Venta $venta): View
    {
        $venta->load(['cliente', 'detalles.producto', 'cuentaFinanciera', 'usuarioRegistra']);
        return view('ventas.show', compact('venta'));
    }
}

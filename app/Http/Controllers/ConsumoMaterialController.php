<?php

namespace App\Http\Controllers;

use App\Models\ConsumoMaterial;
use App\Models\OrdenProduccion;
use App\Models\Kardex;
use App\Services\KardexService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ConsumoMaterialController extends Controller
{
    public function store(Request $request, OrdenProduccion $ordenProduccion, KardexService $kardexService): RedirectResponse
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'tarea_produccion_id' => 'nullable|exists:tareas_produccion,id',
        ]);

        try {
            DB::beginTransaction();

            // Verificar que haya stock suficiente.
            // Para el costo promedio de SALIDA, lo normal es registrarlo al precio promedio ponderado actual.
            // El KardexService se encarga de lanzar excepción si no hay stock o si el cálculo falla,
            // pero para grabar ConsumoMaterial necesitamos el costo que determinó el Kárdex.
            
            // Registramos el movimiento de Kárdex PRIMERO
            $kardexMovimiento = $kardexService->registrarMovimiento(
                $validated['producto_id'],
                Kardex::TIPO_SALIDA,
                $validated['cantidad'],
                0, // Precio unitario es 0 en entrada a la función de SALIDA, el servicio lo calculará usando el costo promedio
                Auth::id(),
                'OrdenProduccion',
                $ordenProduccion->id,
                'Consumo en OP N° ' . $ordenProduccion->numero_orden
            );

            // Una vez que el movimiento de salida se creó, el Kardex calculó el precio_unitario (costo de salida)
            // y el costo_total. Lo leemos de ahí para guardarlo en ConsumoMaterial y llevar los costos de la orden.
            $costo_unitario = $kardexMovimiento->precio_unitario;
            $costo_total = $kardexMovimiento->costo_total;

            ConsumoMaterial::create([
                'orden_produccion_id' => $ordenProduccion->id,
                'producto_id' => $validated['producto_id'],
                'cantidad' => $validated['cantidad'],
                'costo_unitario' => $costo_unitario,
                'costo_total' => $costo_total,
                'tarea_produccion_id' => $validated['tarea_produccion_id'] ?? null,
                'usuario_registra_id' => Auth::id(),
                'fecha_consumo' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Material retirado del almacén y cargado a la orden exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al consumir material: ' . $e->getMessage()]);
        }
    }
}

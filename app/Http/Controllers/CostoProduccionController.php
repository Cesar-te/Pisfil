<?php

namespace App\Http\Controllers;

use App\Models\CostoProduccion;
use App\Models\OrdenProduccion;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostoProduccionController extends Controller
{
    public function store(Request $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $validated = $request->validate([
            'tipo' => 'required|in:mano_obra,gasto_indirecto,servicio',
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
        ]);

        $validated['orden_produccion_id'] = $ordenProduccion->id;
        $validated['usuario_id'] = Auth::id();

        $costo = CostoProduccion::create($validated);
        AuditoriaService::registrar('produccion.costo_adicional', $costo, null, $costo->toArray());

        return back()->with('success', 'Costo adicional registrado en la orden de produccion.');
    }
}

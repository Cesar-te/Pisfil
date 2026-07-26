<?php

namespace App\Http\Controllers;

use App\Models\CuentaContable;
use Illuminate\Http\Request;

class CuentaContableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cuentas = CuentaContable::with('padre')->orderBy('codigo')->get();
        return view('contabilidad.cuentas.index', compact('cuentas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cuentasPrincipales = CuentaContable::whereNull('padre_id')->orWhere('nivel', '<', 5)->orderBy('codigo')->get();
        return view('contabilidad.cuentas.create', compact('cuentasPrincipales'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:cuentas_contables,codigo',
            'descripcion' => 'required|string|max:255',
            'elemento' => 'required|string|max:5',
            'nivel' => 'required|integer|min:2|max:6',
            'tipo' => 'nullable|string|max:50',
            'padre_id' => 'nullable|exists:cuentas_contables,id',
            'estado' => 'boolean'
        ]);

        CuentaContable::create($request->all());

        return redirect()->route('cuentas-contables.index')
            ->with('success', 'Cuenta contable creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CuentaContable $cuentaContable)
    {
        return view('contabilidad.cuentas.show', compact('cuentaContable'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CuentaContable $cuentaContable)
    {
        $cuentasPrincipales = CuentaContable::where('id', '!=', $cuentaContable->id)
                                ->where(function($q) {
                                    $q->whereNull('padre_id')->orWhere('nivel', '<', 5);
                                })
                                ->orderBy('codigo')
                                ->get();
                                
        return view('contabilidad.cuentas.edit', compact('cuentaContable', 'cuentasPrincipales'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CuentaContable $cuentaContable)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:cuentas_contables,codigo,' . $cuentaContable->id,
            'descripcion' => 'required|string|max:255',
            'elemento' => 'required|string|max:5',
            'nivel' => 'required|integer|min:2|max:6',
            'tipo' => 'nullable|string|max:50',
            'padre_id' => 'nullable|exists:cuentas_contables,id',
            'estado' => 'boolean'
        ]);

        $cuentaContable->update($request->all());

        return redirect()->route('cuentas-contables.index')
            ->with('success', 'Cuenta contable actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuentaContable $cuentaContable)
    {
        // Add basic logic to prevent deleting accounts with children or transactions
        if ($cuentaContable->subcuentas()->count() > 0) {
            return redirect()->route('cuentas-contables.index')
                ->with('error', 'No se puede eliminar la cuenta porque tiene subcuentas.');
        }

        $cuentaContable->delete();

        return redirect()->route('cuentas-contables.index')
            ->with('success', 'Cuenta contable eliminada exitosamente.');
    }
}

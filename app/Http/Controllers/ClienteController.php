<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::orderBy('nombre')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento_identidad' => 'required|string|max:20|unique:clientes',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:100',
        ]);

        Cliente::create($validated);
        return back()->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento_identidad' => ['required', 'string', 'max:20', Rule::unique('clientes')->ignore($cliente->id)],
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:100',
            'estado' => 'required|boolean',
        ]);

        $cliente->update($validated);
        return back()->with('success', 'Cliente actualizado correctamente.');
    }
}

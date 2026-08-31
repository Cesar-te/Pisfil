<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = User::with('rol')->orderBy('name')->paginate(15);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        $roles = Rol::where('estado', true)->get();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'documento_identidad' => ['required', 'digits_between:8,12', Rule::unique('users')],
            'telefono' => ['nullable', 'regex:/^[0-9+()\s-]{6,20}$/'],
            'rol_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['estado'] = true;

        User::create($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(User $usuario): View
    {
        $roles = Rol::where('estado', true)->get();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'documento_identidad' => ['required', 'digits_between:8,12', Rule::unique('users')->ignore($usuario->id)],
            'telefono' => ['nullable', 'regex:/^[0-9+()\s-]{6,20}$/'],
            'rol_id' => 'required|exists:roles,id',
            'estado' => 'required|boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $validated['password'] = Hash::make($request->password);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }
}

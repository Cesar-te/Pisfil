<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $auditorias = Auditoria::with('usuario')
            ->when($request->filled('accion'), fn ($query) => $query->where('accion', $request->accion))
            ->when($request->filled('entidad'), fn ($query) => $query->where('entidad_tipo', $request->entidad))
            ->when($request->filled('desde'), fn ($query) => $query->whereDate('created_at', '>=', $request->desde))
            ->when($request->filled('hasta'), fn ($query) => $query->whereDate('created_at', '<=', $request->hasta))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $acciones = Auditoria::select('accion')->distinct()->orderBy('accion')->pluck('accion');
        $entidades = Auditoria::select('entidad_tipo')->whereNotNull('entidad_tipo')->distinct()->orderBy('entidad_tipo')->pluck('entidad_tipo');

        return view('auditorias.index', compact('auditorias', 'acciones', 'entidades'));
    }
}

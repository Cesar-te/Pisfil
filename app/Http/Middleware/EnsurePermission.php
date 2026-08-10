<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $usuario = $request->user();

        if (!$usuario || !$usuario->hasPermission($permiso)) {
            abort(403, 'No tienes permiso para realizar esta accion.');
        }

        return $next($request);
    }
}

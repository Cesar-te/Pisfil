<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuditoriaService
{
    public static function registrar(
        string $accion,
        ?Model $entidad = null,
        ?array $valoresAnteriores = null,
        ?array $valoresNuevos = null
    ): void {
        try {
            $request = request();

            Auditoria::create([
                'usuario_id' => Auth::id(),
                'accion' => $accion,
                'entidad_tipo' => $entidad ? class_basename($entidad) : null,
                'entidad_id' => $entidad?->getKey(),
                'valores_anteriores' => $valoresAnteriores,
                'valores_nuevos' => $valoresNuevos,
                'ip' => $request?->ip(),
                'user_agent' => Str::limit((string) $request?->userAgent(), 255, ''),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('No se pudo registrar auditoria.', [
                'accion' => $accion,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}

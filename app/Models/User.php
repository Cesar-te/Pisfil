<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rol_id', 'documento_identidad', 'telefono', 'estado'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    /**
     * Relación con Rol
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Comprueba si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permiso): bool
    {
        if (!$this->rol || !$this->rol->estado) {
            return false;
        }
        return $this->rol->hasPermission($permiso);
    }

    /**
     * Relación con Órdenes de Producción creadas
     */
    public function ordenesCreadas(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class, 'usuario_creador_id');
    }

    /**
     * Relación con Órdenes de Producción asignadas
     */
    public function ordenesAsignadas(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class, 'usuario_asignado_id');
    }

    /**
     * Relación con Tareas asignadas
     */
    public function tareasAsignadas(): HasMany
    {
        return $this->hasMany(TareaProduccion::class, 'usuario_responsable_id');
    }

    /**
     * Relación con Movimientos de Kardex
     */
    public function movimientosKardex(): HasMany
    {
        return $this->hasMany(Kardex::class);
    }

    /**
     * Relación con Reportes de Tareas
     */
    public function reportesTareas(): HasMany
    {
        return $this->hasMany(ReporteTarea::class);
    }

    /**
     * Relación con Incidencias reportadas
     */
    public function incidenciasReportadas(): HasMany
    {
        return $this->hasMany(IncidenciaReproceso::class, 'usuario_reporta_id');
    }

    /**
     * Relación con Incidencias asignadas
     */
    public function incidenciasAsignadas(): HasMany
    {
        return $this->hasMany(IncidenciaReproceso::class, 'usuario_asigna_id');
    }
}

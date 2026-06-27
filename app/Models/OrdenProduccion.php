<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenProduccion extends Model
{
    use HasFactory;

    protected $table = 'ordenes_produccion';

    protected $fillable = [
        'numero_orden',
        'cliente',
        'descripcion_trabajo',
        'estado',
        'fecha_inicio_planificada',
        'fecha_fin_planificada',
        'fecha_inicio_real',
        'fecha_fin_real',
        'observaciones',
        'usuario_creador_id',
        'usuario_asignado_id',
    ];

    protected $casts = [
        'fecha_inicio_planificada' => 'datetime',
        'fecha_fin_planificada' => 'datetime',
        'fecha_inicio_real' => 'datetime',
        'fecha_fin_real' => 'datetime',
    ];

    /**
     * Estados posibles
     */
    public const ESTADO_PLANIFICADA = 'planificada';
    public const ESTADO_EN_PROCESO = 'en_proceso';
    public const ESTADO_PAUSADA = 'pausada';
    public const ESTADO_COMPLETADA = 'completada';
    public const ESTADO_CANCELADA = 'cancelada';

    /**
     * Relación con Usuario Creador
     */
    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_creador_id');
    }

    /**
     * Relación con Usuario Asignado
     */
    public function usuarioAsignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_asignado_id');
    }

    /**
     * Relación con Tareas
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(TareaProduccion::class);
    }

    /**
     * Relación con Consumo de Materiales
     */
    public function consumoMateriales(): HasMany
    {
        return $this->hasMany(ConsumoMaterial::class);
    }

    /**
     * Relación con Procesos de Producción
     */
    public function procesos(): HasMany
    {
        return $this->hasMany(ProcesoProduccion::class);
    }
}

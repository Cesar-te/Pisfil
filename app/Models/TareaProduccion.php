<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TareaProduccion extends Model
{
    use HasFactory;

    protected $table = 'tareas_produccion';

    protected $fillable = [
        'orden_produccion_id',
        'numero_tarea',
        'nombre',
        'descripcion',
        'proceso_produccion_id',
        'estado',
        'fecha_inicio_planificada',
        'fecha_fin_planificada',
        'fecha_inicio_real',
        'fecha_fin_real',
        'usuario_responsable_id',
        'porcentaje_avance',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio_planificada' => 'datetime',
        'fecha_fin_planificada' => 'datetime',
        'fecha_inicio_real' => 'datetime',
        'fecha_fin_real' => 'datetime',
        'porcentaje_avance' => 'integer',
    ];

    /**
     * Estados posibles
     */
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_EN_PROGRESO = 'en_progreso';
    public const ESTADO_COMPLETADA = 'completada';
    public const ESTADO_REPROCESO = 'reproceso';

    /**
     * Relación con OrdenProduccion
     */
    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class);
    }

    /**
     * Relación con Usuario Responsable
     */
    public function usuarioResponsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_responsable_id');
    }

    /**
     * Relación con Proceso de Producción
     */
    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoProduccion::class, 'proceso_produccion_id');
    }

    /**
     * Relación con Incidencias
     */
    public function incidencias(): HasMany
    {
        return $this->hasMany(IncidenciaReproceso::class);
    }

    /**
     * Relación con Reportes de Tareas
     */
    public function reportes(): HasMany
    {
        return $this->hasMany(ReporteTarea::class);
    }
}

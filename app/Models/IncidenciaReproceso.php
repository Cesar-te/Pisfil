<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidenciaReproceso extends Model
{
    use HasFactory;

    protected $table = 'incidencias_reproceso';

    protected $fillable = [
        'tarea_produccion_id',
        'tipo_incidencia',
        'descripcion',
        'causa_raiz',
        'usuario_reporta_id',
        'usuario_asigna_id',
        'fecha_incidencia',
        'fecha_resolucion',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_incidencia' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    /**
     * Tipos de incidencia
     */
    public const TIPO_DEFECTO_CALIDAD = 'defecto_calidad';
    public const TIPO_FALTA_MATERIAL = 'falta_material';
    public const TIPO_EQUIPO_AVERIADO = 'equipo_averiado';
    public const TIPO_OTRO = 'otro';

    /**
     * Estados
     */
    public const ESTADO_ABIERTA = 'abierta';
    public const ESTADO_EN_REVISION = 'en_revision';
    public const ESTADO_RESUELTA = 'resuelta';

    /**
     * Relación con TareaProduccion
     */
    public function tarea(): BelongsTo
    {
        return $this->belongsTo(TareaProduccion::class, 'tarea_produccion_id');
    }

    /**
     * Relación con Usuario que reporta
     */
    public function usuarioReporta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_reporta_id');
    }

    /**
     * Relación con Usuario que asigna
     */
    public function usuarioAsigna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_asigna_id');
    }
}

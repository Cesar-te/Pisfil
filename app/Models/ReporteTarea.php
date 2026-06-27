<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteTarea extends Model
{
    use HasFactory;

    protected $table = 'reportes_tareas';

    protected $fillable = [
        'tarea_produccion_id',
        'usuario_id',
        'fecha_reporte',
        'porcentaje_avance',
        'horas_trabajadas',
        'descripcion_trabajo_realizado',
        'observaciones',
        'detalles_adicionales',
    ];

    protected $casts = [
        'fecha_reporte' => 'datetime',
        'porcentaje_avance' => 'integer',
        'horas_trabajadas' => 'decimal:2',
    ];

    /**
     * Relación con TareaProduccion
     */
    public function tarea(): BelongsTo
    {
        return $this->belongsTo(TareaProduccion::class);
    }

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcesoProduccion extends Model
{
    use HasFactory;

    protected $table = 'procesos_produccion';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'duracion_estimada',
        'duracion_estimada_unidad',
        'orden_secuencia',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Unidades válidas para duración
     */
    public const DURACION_MINUTOS = 'minutos';
    public const DURACION_HORAS = 'horas';
    public const DURACION_DIAS = 'dias';

    /**
     * Relación con Tareas
     */
    public function tareas(): HasMany
    {
        return $this->hasMany(TareaProduccion::class);
    }

    /**
     * Relación con Órdenes de Producción
     */
    public function ordenes(): HasMany
    {
        return $this->hasMany(OrdenProduccion::class);
    }
}

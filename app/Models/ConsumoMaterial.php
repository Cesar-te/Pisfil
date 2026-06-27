<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumoMaterial extends Model
{
    use HasFactory;

    protected $table = 'consumos_material';

    protected $fillable = [
        'orden_produccion_id',
        'producto_id',
        'cantidad_planificada',
        'cantidad_consumida',
        'unidad_medida_id',
        'precio_unitario',
        'costo_total',
        'observaciones',
    ];

    protected $casts = [
        'cantidad_planificada' => 'decimal:2',
        'cantidad_consumida' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    /**
     * Relación con OrdenProduccion
     */
    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class);
    }

    /**
     * Relación con Producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con Unidad de Medida
     */
    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class);
    }
}

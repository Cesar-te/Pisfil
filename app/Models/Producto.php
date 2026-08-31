<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria_id',
        'unidad_medida_id',
        'precio_unitario',
        'stock_minimo',
        'stock_maximo',
        'stock_actual',
        'estado',
        'notas',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'stock_maximo' => 'decimal:2',
        'stock_actual' => 'decimal:2',
    ];

    /**
     * Relación con Categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación con Unidad de Medida
     */
    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    /**
     * Relación con Kardex (movimientos)
     */
    public function movimientosKardex(): HasMany
    {
        return $this->hasMany(Kardex::class);
    }

    /**
     * Relación con consumo en órdenes de producción
     */
    public function materialesConsumo(): HasMany
    {
        return $this->hasMany(ConsumoMaterial::class);
    }

    /**
     * Relación con entrada de compras
     */
    public function entradas(): HasMany
    {
        return $this->hasMany(EntradaCompra::class);
    }
}

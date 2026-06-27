<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleEntradaCompra extends Model
{
    use HasFactory;

    protected $table = 'detalles_entrada_compra';

    protected $fillable = [
        'entrada_compra_id',
        'producto_id',
        'cantidad_solicitada',
        'cantidad_recibida',
        'precio_unitario',
        'costo_total',
        'observaciones',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:2',
        'cantidad_recibida' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    /**
     * Relación con EntradaCompra
     */
    public function entrada(): BelongsTo
    {
        return $this->belongsTo(EntradaCompra::class);
    }

    /**
     * Relación con Producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}

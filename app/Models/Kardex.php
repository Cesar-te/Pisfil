<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $fillable = [
        'producto_id',
        'tipo_movimiento',
        'cantidad',
        'precio_unitario',
        'saldo_anterior',
        'saldo_actual',
        'referencia_id',
        'referencia_tipo',
        'usuario_id',
        'observaciones',
        'fecha_movimiento',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'saldo_anterior' => 'decimal:2',
        'saldo_actual' => 'decimal:2',
        'fecha_movimiento' => 'datetime',
    ];

    /**
     * Tipos de movimiento válidos
     */
    public const TIPO_ENTRADA = 'entrada';
    public const TIPO_SALIDA = 'salida';
    public const TIPO_AJUSTE = 'ajuste';
    public const TIPO_DEVOLUCCION = 'devolucion';

    /**
     * Relación con Producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

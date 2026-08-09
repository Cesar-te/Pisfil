<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntradaCompra extends Model
{
    use HasFactory;

    protected $table = 'entradas_compra';

    protected $fillable = [
        'numero_documento',
        'proveedor_id',
        'fecha_emision',
        'fecha_recepcion',
        'estado',
        'observaciones',
        'usuario_id',
        'estado_pago',
        'monto_pagado',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_recepcion' => 'datetime',
    ];

    /**
     * Estados Logísticos
     */
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_RECIBIDA = 'recibida';
    public const ESTADO_VALIDADA = 'validada';
    public const ESTADO_RECHAZADA = 'rechazada';

    /**
     * Estados de Pago
     */
    public const PAGO_PENDIENTE = 'pendiente';
    public const PAGO_PARCIAL = 'parcial';
    public const PAGO_PAGADO = 'pagado';

    /**
     * Relación con Proveedor
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Detalles de Entrada
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleEntradaCompra::class);
    }

    public function asientosContables(): HasMany
    {
        return $this->hasMany(AsientoContable::class, 'origen_id')
            ->where('origen_tipo', 'EntradaCompra');
    }
}

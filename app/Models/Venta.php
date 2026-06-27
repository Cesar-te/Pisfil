<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'tipo_comprobante',
        'serie_comprobante',
        'numero_comprobante',
        'fecha_venta',
        'moneda',
        'total',
        'estado', // borrador, pagada, anulada
        'cuenta_financiera_id',
        'usuario_registra_id'
    ];

    protected $casts = [
        'fecha_venta' => 'date',
        'total' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function cuentaFinanciera(): BelongsTo
    {
        return $this->belongsTo(CuentaFinanciera::class, 'cuenta_financiera_id');
    }

    public function usuarioRegistra(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registra_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }
}

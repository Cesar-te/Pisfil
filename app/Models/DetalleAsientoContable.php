<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleAsientoContable extends Model
{
    use HasFactory;

    protected $table = 'detalle_asientos_contables';

    protected $fillable = [
        'asiento_contable_id',
        'cuenta_contable_id',
        'tipo_movimiento',
        'monto',
        'glosa',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(AsientoContable::class, 'asiento_contable_id');
    }

    public function cuentaContable(): BelongsTo
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contable_id');
    }
}

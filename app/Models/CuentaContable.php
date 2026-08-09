<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaContable extends Model
{
    use HasFactory;

    protected $table = 'cuentas_contables';

    protected $fillable = [
        'codigo',
        'descripcion',
        'elemento',
        'nivel',
        'tipo',
        'padre_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'nivel' => 'integer',
    ];

    /**
     * Cuenta superior / Padre
     */
    public function padre(): BelongsTo
    {
        return $this->belongsTo(CuentaContable::class, 'padre_id');
    }

    /**
     * Subcuentas / Hijos
     */
    public function subcuentas(): HasMany
    {
        return $this->hasMany(CuentaContable::class, 'padre_id');
    }

    /**
     * Transacciones financieras asociadas
     */
    public function transaccionesFinancieras(): HasMany
    {
        return $this->hasMany(TransaccionFinanciera::class, 'cuenta_contable_id');
    }

    public function detalleAsientos(): HasMany
    {
        return $this->hasMany(DetalleAsientoContable::class, 'cuenta_contable_id');
    }
}

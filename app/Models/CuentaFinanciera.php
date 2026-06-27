<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaFinanciera extends Model
{
    use HasFactory;

    protected $table = 'cuentas_financieras';

    protected $fillable = [
        'nombre',
        'tipo',
        'banco',
        'numero_cuenta',
        'moneda',
        'saldo_actual',
        'estado'
    ];

    protected $casts = [
        'saldo_actual' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function transacciones(): HasMany
    {
        return $this->hasMany(TransaccionFinanciera::class, 'cuenta_financiera_id');
    }

    public function transaccionesDestino(): HasMany
    {
        return $this->hasMany(TransaccionFinanciera::class, 'cuenta_destino_id');
    }
}

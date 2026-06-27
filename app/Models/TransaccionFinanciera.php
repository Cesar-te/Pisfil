<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionFinanciera extends Model
{
    use HasFactory;

    protected $table = 'transacciones_financieras';

    protected $fillable = [
        'cuenta_financiera_id',
        'tipo', // ingreso, egreso, transferencia
        'monto',
        'motivo',
        'referencia',
        'fecha_transaccion',
        'usuario_registra_id',
        'cuenta_destino_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_transaccion' => 'date',
    ];

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(CuentaFinanciera::class, 'cuenta_financiera_id');
    }

    public function cuentaDestino(): BelongsTo
    {
        return $this->belongsTo(CuentaFinanciera::class, 'cuenta_destino_id');
    }

    public function usuarioRegistra(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registra_id');
    }
}

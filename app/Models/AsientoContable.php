<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsientoContable extends Model
{
    use HasFactory;

    protected $table = 'asientos_contables';

    protected $fillable = [
        'numero',
        'fecha',
        'descripcion',
        'origen_tipo',
        'origen_id',
        'moneda',
        'total_debe',
        'total_haber',
        'estado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_debe' => 'decimal:2',
        'total_haber' => 'decimal:2',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleAsientoContable::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

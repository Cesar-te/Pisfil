<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo',
        'nombre_empresa',
        'nombre_contacto',
        'documento_identidad',
        'ruc',
        'email',
        'telefono',
        'celular',
        'direccion',
        'ciudad',
        'pais',
        'condicion_pago',
        'plazo_entrega',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con Entradas de Compra
     */
    public function entradas(): HasMany
    {
        return $this->hasMany(EntradaCompra::class);
    }
}

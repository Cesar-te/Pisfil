<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'unidades_medida';

    protected $fillable = [
        'codigo',
        'nombre',
        'simbolo',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con Productos
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'unidad_medida_id');
    }
}

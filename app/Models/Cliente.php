<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'documento_identidad',
        'direccion',
        'telefono',
        'email',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];
}

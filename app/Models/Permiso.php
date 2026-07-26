<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    /**
     * Relación con Roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'rol_permiso');
    }
}

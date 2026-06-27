<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
        'permisos_json',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'permisos_json' => 'array',
    ];

    /**
     * Relación con Usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    /**
     * Comprueba si el rol tiene un permiso específico
     */
    public function hasPermission(string $permiso): bool
    {
        if (empty($this->permisos_json)) {
            return false;
        }
        return in_array($permiso, (array) $this->permisos_json);
    }
}

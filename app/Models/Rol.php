<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con Usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    /**
     * Relación con Permisos
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso');
    }

    /**
     * Comprueba si el rol tiene un permiso específico
     */
    public function hasPermission(string $permisoCodigo): bool
    {
        if ($this->permisos()->where('codigo', '*')->exists()) {
            return true;
        }
        return $this->permisos()->where('codigo', $permisoCodigo)->exists();
    }
}

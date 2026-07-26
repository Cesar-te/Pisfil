<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'url',
        'icono',
        'padre_id',
        'orden',
        'permiso_id',
    ];

    public function submenus(): HasMany
    {
        return $this->hasMany(Menu::class, 'padre_id')->orderBy('orden');
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'padre_id');
    }

    public function permiso(): BelongsTo
    {
        return $this->belongsTo(Permiso::class, 'permiso_id');
    }
}

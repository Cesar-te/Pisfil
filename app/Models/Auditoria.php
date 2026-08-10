<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    use HasFactory;

    protected $table = 'auditorias';

    protected $fillable = [
        'usuario_id',
        'accion',
        'entidad_tipo',
        'entidad_id',
        'valores_anteriores',
        'valores_nuevos',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

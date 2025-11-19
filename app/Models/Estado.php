<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estado';

    protected $fillable = [
        'uf',
        'nome_estado',
        'pais_id',
    ];

    /**
     * Relacionamento: Um estado pertence a um país
     */
    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    /**
     * Relacionamento: Um estado tem muitas cidades
     */
    public function cidades(): HasMany
    {
        return $this->hasMany(Cidade::class, 'estado_id');
    }
}


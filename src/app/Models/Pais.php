<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'pais';

    protected $fillable = [
        'nome_pais',
        'sigla',
    ];

    /**
     * Relacionamento: Um país tem muitos estados
     */
    public function estados(): HasMany
    {
        return $this->hasMany(Estado::class, 'pais_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaMissao extends Model
{
    use HasFactory;

    protected $table = 'categorias_missao';

    protected $fillable = [
        'nome',
        'descricao',
        'icone',
    ];

    /**
     * Relacionamento: Uma categoria tem muitas missões
     */
    public function missoes(): HasMany
    {
        return $this->hasMany(Missao::class, 'categoria_id');
    }
}


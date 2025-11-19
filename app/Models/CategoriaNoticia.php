<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaNoticia extends Model
{
    use HasFactory;

    protected $table = 'categorias_noticia';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    /**
     * Relacionamento: Uma categoria tem muitas notícias
     */
    public function noticias(): HasMany
    {
        return $this->hasMany(Noticia::class, 'categoria_id');
    }
}


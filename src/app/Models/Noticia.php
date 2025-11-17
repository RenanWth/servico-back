<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Noticia extends Model
{
    use HasFactory;

    protected $table = 'noticias';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'conteudo',
        'categoria_id',
        'admin_autor_id',
        'destaque',
        'status',
        'dt_publicacao',
        'dt_atualizacao',
        'visualizacoes',
    ];

    protected $casts = [
        'destaque' => 'boolean',
        'dt_publicacao' => 'datetime',
        'dt_atualizacao' => 'datetime',
        'visualizacoes' => 'integer',
    ];

    /**
     * Relacionamento: Uma notícia pertence a uma categoria
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaNoticia::class, 'categoria_id');
    }

    /**
     * Relacionamento: Uma notícia foi criada por uma pessoa (admin)
     */
    public function adminAutor(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'admin_autor_id');
    }

    /**
     * Relacionamento: Uma notícia tem muitas imagens
     */
    public function imagens(): HasMany
    {
        return $this->hasMany(ImagemNoticia::class, 'noticia_id');
    }
}


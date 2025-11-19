<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagemNoticia extends Model
{
    use HasFactory;

    protected $table = 'imagens_noticia';

    protected $fillable = [
        'noticia_id',
        'url',
        'legenda',
        'ordem',
        'principal',
        'dt_upload',
    ];

    protected $casts = [
        'ordem' => 'integer',
        'principal' => 'boolean',
        'dt_upload' => 'datetime',
    ];

    /**
     * Relacionamento: Uma imagem pertence a uma notícia
     */
    public function noticia(): BelongsTo
    {
        return $this->belongsTo(Noticia::class, 'noticia_id');
    }
}


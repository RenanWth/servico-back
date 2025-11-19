<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endereco extends Model
{
    use HasFactory;

    protected $table = 'enderecos';

    protected $fillable = [
        'pessoa_id',
        'cidades_id',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'principal',
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    /**
     * Relacionamento: Um endereço pertence a uma pessoa
     */
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    /**
     * Relacionamento: Um endereço pertence a uma cidade
     */
    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class, 'cidades_id');
    }
}


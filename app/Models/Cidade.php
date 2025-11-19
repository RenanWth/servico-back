<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cidade extends Model
{
    use HasFactory;

    protected $table = 'cidades';

    protected $fillable = [
        'nome_cidade',
        'estado_id',
        'cod_ibge',
    ];

    /**
     * Relacionamento: Uma cidade pertence a um estado
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    /**
     * Relacionamento: Uma cidade tem muitos endereços
     */
    public function enderecos(): HasMany
    {
        return $this->hasMany(Endereco::class, 'cidades_id');
    }

    /**
     * Relacionamento: Uma cidade tem muitas missões
     */
    public function missoes(): HasMany
    {
        return $this->hasMany(Missao::class, 'cidades_id');
    }

    /**
     * Relacionamento: Uma cidade tem muitos pontos de coleta
     */
    public function pontosColeta(): HasMany
    {
        return $this->hasMany(PontoColeta::class, 'cidades_id');
    }
}


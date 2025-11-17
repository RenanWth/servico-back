<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PontoColeta extends Model
{
    use HasFactory;

    protected $table = 'pontos_coleta';

    protected $fillable = [
        'nome',
        'descricao',
        'cidades_id',
        'endereco',
        'latitude',
        'longitude',
        'telefone',
        'horario_funcionamento',
        'responsavel_nome',
        'responsavel_telefone',
        'ativo',
        'dt_criacao',
        'admin_criador_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ativo' => 'boolean',
        'dt_criacao' => 'datetime',
    ];

    /**
     * Relacionamento: Um ponto de coleta pertence a uma cidade
     */
    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class, 'cidades_id');
    }

    /**
     * Relacionamento: Um ponto de coleta foi criado por uma pessoa (admin)
     */
    public function adminCriador(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'admin_criador_id');
    }

    /**
     * Relacionamento: Um ponto de coleta tem muitas necessidades
     */
    public function necessidades(): HasMany
    {
        return $this->hasMany(NecessidadePonto::class, 'ponto_coleta_id');
    }

    /**
     * Relacionamento: Um ponto de coleta recebe muitas doações
     */
    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class, 'ponto_coleta_id');
    }
}


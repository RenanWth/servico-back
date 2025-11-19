<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Missao extends Model
{
    use HasFactory;

    protected $table = 'missoes';

    protected $fillable = [
        'titulo',
        'descricao',
        'categoria_id',
        'local_encontro',
        'cidades_id',
        'latitude',
        'longitude',
        'dt_inicio',
        'dt_fim',
        'vagas_totais',
        'vagas_preenchidas',
        'admin_criador_id',
        'status',
        'dt_criacao',
        'dt_atualizacao',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'dt_inicio' => 'datetime',
        'dt_fim' => 'datetime',
        'dt_criacao' => 'datetime',
        'dt_atualizacao' => 'datetime',
        'vagas_totais' => 'integer',
        'vagas_preenchidas' => 'integer',
    ];

    /**
     * Relacionamento: Uma missão pertence a uma categoria
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaMissao::class, 'categoria_id');
    }

    /**
     * Relacionamento: Uma missão pertence a uma cidade
     */
    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class, 'cidades_id');
    }

    /**
     * Relacionamento: Uma missão foi criada por uma pessoa (admin)
     */
    public function adminCriador(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'admin_criador_id');
    }

    /**
     * Relacionamento: Uma missão tem muitas candidaturas
     */
    public function candidaturas(): HasMany
    {
        return $this->hasMany(CandidaturaMissao::class, 'missao_id');
    }
}


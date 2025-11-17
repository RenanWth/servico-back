<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidaturaMissao extends Model
{
    use HasFactory;

    protected $table = 'candidaturas_missao';

    protected $fillable = [
        'missao_id',
        'voluntario_id',
        'status',
        'dt_candidatura',
        'dt_aprovacao',
        'dt_conclusao',
        'avaliacao',
        'obs_avaliacao',
    ];

    protected $casts = [
        'dt_candidatura' => 'datetime',
        'dt_aprovacao' => 'datetime',
        'dt_conclusao' => 'datetime',
        'avaliacao' => 'integer',
    ];

    /**
     * Relacionamento: Uma candidatura pertence a uma missão
     */
    public function missao(): BelongsTo
    {
        return $this->belongsTo(Missao::class, 'missao_id');
    }

    /**
     * Relacionamento: Uma candidatura pertence a um voluntário
     */
    public function voluntario(): BelongsTo
    {
        return $this->belongsTo(Voluntario::class, 'voluntario_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voluntario extends Model
{
    use HasFactory;

    protected $table = 'voluntarios';

    protected $fillable = [
        'pessoa_id',
        'escolaridade',
        'profissao',
        'habilidades',
        'disponibilidade',
        'exp_emergencias',
        'cnh_categoria',
        'possui_veiculo',
        'dt_aprovacao',
        'status',
        'obs',
    ];

    protected $casts = [
        'possui_veiculo' => 'boolean',
        'dt_aprovacao' => 'datetime',
    ];

    /**
     * Relacionamento: Um voluntário pertence a uma pessoa
     */
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    /**
     * Relacionamento: Um voluntário tem muitas candidaturas
     */
    public function candidaturas(): HasMany
    {
        return $this->hasMany(CandidaturaMissao::class, 'voluntario_id');
    }
}


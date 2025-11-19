<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doacao extends Model
{
    use HasFactory;

    protected $table = 'doacoes';

    protected $fillable = [
        'pessoa_id',
        'ponto_coleta_id',
        'dt_doacao',
        'dt_entrega',
        'status',
        'obs',
    ];

    protected $casts = [
        'dt_doacao' => 'datetime',
        'dt_entrega' => 'datetime',
    ];

    /**
     * Relacionamento: Uma doação pertence a uma pessoa
     */
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    /**
     * Relacionamento: Uma doação pertence a um ponto de coleta
     */
    public function pontoColeta(): BelongsTo
    {
        return $this->belongsTo(PontoColeta::class, 'ponto_coleta_id');
    }

    /**
     * Relacionamento: Uma doação tem muitos itens
     */
    public function itens(): HasMany
    {
        return $this->hasMany(ItemDoacao::class, 'doacao_id');
    }
}


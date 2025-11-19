<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NecessidadePonto extends Model
{
    use HasFactory;

    protected $table = 'necessidades_ponto';

    protected $fillable = [
        'ponto_coleta_id',
        'tipo_item_id',
        'quantidade_necessaria',
        'quantidade_recebida',
        'prioridade',
        'dt_criacao',
        'dt_atualizacao',
        'ativo',
    ];

    protected $casts = [
        'quantidade_necessaria' => 'decimal:2',
        'quantidade_recebida' => 'decimal:2',
        'dt_criacao' => 'datetime',
        'dt_atualizacao' => 'datetime',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento: Uma necessidade pertence a um ponto de coleta
     */
    public function pontoColeta(): BelongsTo
    {
        return $this->belongsTo(PontoColeta::class, 'ponto_coleta_id');
    }

    /**
     * Relacionamento: Uma necessidade pertence a um tipo de item
     */
    public function tipoItem(): BelongsTo
    {
        return $this->belongsTo(TipoItem::class, 'tipo_item_id');
    }
}


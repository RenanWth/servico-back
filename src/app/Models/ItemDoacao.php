<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemDoacao extends Model
{
    use HasFactory;

    protected $table = 'itens_doacao';

    protected $fillable = [
        'doacao_id',
        'tipo_item_id',
        'quantidade',
        'obs',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
    ];

    /**
     * Relacionamento: Um item de doação pertence a uma doação
     */
    public function doacao(): BelongsTo
    {
        return $this->belongsTo(Doacao::class, 'doacao_id');
    }

    /**
     * Relacionamento: Um item de doação pertence a um tipo de item
     */
    public function tipoItem(): BelongsTo
    {
        return $this->belongsTo(TipoItem::class, 'tipo_item_id');
    }
}


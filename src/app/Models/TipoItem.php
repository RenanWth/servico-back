<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoItem extends Model
{
    use HasFactory;

    protected $table = 'tipos_item';

    protected $fillable = [
        'nome',
        'descricao',
        'unidade_medida',
        'categoria',
    ];

    /**
     * Relacionamento: Um tipo de item tem muitas necessidades de ponto
     */
    public function necessidadesPonto(): HasMany
    {
        return $this->hasMany(NecessidadePonto::class, 'tipo_item_id');
    }

    /**
     * Relacionamento: Um tipo de item tem muitos itens de doação
     */
    public function itensDoacao(): HasMany
    {
        return $this->hasMany(ItemDoacao::class, 'tipo_item_id');
    }
}


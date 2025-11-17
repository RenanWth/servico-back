<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfis';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    /**
     * Relacionamento: Um perfil tem muitas pessoas
     */
    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class, 'perfil_id');
    }
}


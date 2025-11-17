<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pessoa extends Model
{
    use HasFactory;

    protected $table = 'pessoas';

    protected $fillable = [
        'nome_completo',
        'cpf',
        'email',
        'telefone',
        'dt_nascimento',
        'genero',
        'perfil_id',
        'ativo',
        'dt_cadastro',
        'dt_atualizacao',
    ];

    protected $casts = [
        'dt_nascimento' => 'date',
        'dt_cadastro' => 'datetime',
        'dt_atualizacao' => 'datetime',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento: Uma pessoa pertence a um perfil
     */
    public function perfil(): BelongsTo
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    /**
     * Relacionamento: Uma pessoa tem muitos endereços
     */
    public function enderecos(): HasMany
    {
        return $this->hasMany(Endereco::class, 'pessoa_id');
    }

    /**
     * Relacionamento: Uma pessoa pode ser um voluntário
     */
    public function voluntario(): HasOne
    {
        return $this->hasOne(Voluntario::class, 'pessoa_id');
    }

    /**
     * Relacionamento: Uma pessoa pode criar muitas missões (como admin)
     */
    public function missoesCriadas(): HasMany
    {
        return $this->hasMany(Missao::class, 'admin_criador_id');
    }

    /**
     * Relacionamento: Uma pessoa pode criar muitas notícias (como admin)
     */
    public function noticiasCriadas(): HasMany
    {
        return $this->hasMany(Noticia::class, 'admin_autor_id');
    }

    /**
     * Relacionamento: Uma pessoa pode criar muitos pontos de coleta (como admin)
     */
    public function pontosColetaCriados(): HasMany
    {
        return $this->hasMany(PontoColeta::class, 'admin_criador_id');
    }

    /**
     * Relacionamento: Uma pessoa pode fazer muitas doações
     */
    public function doacoes(): HasMany
    {
        return $this->hasMany(Doacao::class, 'pessoa_id');
    }
}


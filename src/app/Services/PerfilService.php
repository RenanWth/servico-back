<?php

namespace App\Services;

use App\Models\Perfil;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PerfilService
{
    /**
     * Lista todos os perfis
     */
    public function listar(): Collection
    {
        return Perfil::all();
    }

    /**
     * Busca perfil por ID
     */
    public function buscarPorId(int $id): Perfil
    {
        $perfil = Perfil::find($id);
        
        if (!$perfil) {
            throw new ModelNotFoundException("Perfil com ID {$id} não encontrado.");
        }
        
        return $perfil;
    }

    /**
     * Busca perfil por nome
     */
    public function buscarPorNome(string $nome): ?Perfil
    {
        return Perfil::where('nome', $nome)->first();
    }

    /**
     * Cria um novo perfil
     */
    public function criar(array $dados): Perfil
    {
        // Validar nome único
        if ($this->buscarPorNome($dados['nome'])) {
            throw new \InvalidArgumentException("Já existe um perfil com o nome '{$dados['nome']}'.");
        }

        return Perfil::create($dados);
    }

    /**
     * Atualiza um perfil existente
     */
    public function atualizar(int $id, array $dados): Perfil
    {
        $perfil = $this->buscarPorId($id);

        // Validar nome único se estiver alterando
        if (isset($dados['nome']) && $dados['nome'] !== $perfil->nome) {
            if ($this->buscarPorNome($dados['nome'])) {
                throw new \InvalidArgumentException("Já existe um perfil com o nome '{$dados['nome']}'.");
            }
        }

        $perfil->update($dados);
        return $perfil->fresh();
    }

    /**
     * Exclui um perfil
     */
    public function excluir(int $id): bool
    {
        $perfil = $this->buscarPorId($id);

        // Verificar se há pessoas vinculadas
        if ($perfil->pessoas()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir o perfil. Existem pessoas vinculadas a ele.");
        }

        return $perfil->delete();
    }
}


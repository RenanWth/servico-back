<?php

namespace App\Services;

use App\Models\Voluntario;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VoluntarioService
{
    /**
     * Lista todos os voluntários
     */
    public function listar(): Collection
    {
        return Voluntario::with('pessoa')->get();
    }

    /**
     * Lista voluntários por status
     */
    public function listarPorStatus(string $status): Collection
    {
        return Voluntario::where('status', $status)->with('pessoa')->get();
    }

    /**
     * Lista apenas voluntários aprovados
     */
    public function listarAprovados(): Collection
    {
        return Voluntario::where('status', 'aprovado')->with('pessoa')->get();
    }

    /**
     * Busca voluntário por ID
     */
    public function buscarPorId(int $id): Voluntario
    {
        $voluntario = Voluntario::find($id);
        
        if (!$voluntario) {
            throw new ModelNotFoundException("Voluntário com ID {$id} não encontrado.");
        }
        
        return $voluntario;
    }

    /**
     * Busca voluntário por pessoa
     */
    public function buscarPorPessoa(int $pessoaId): ?Voluntario
    {
        return Voluntario::where('pessoa_id', $pessoaId)->first();
    }

    /**
     * Cria um novo voluntário
     */
    public function criar(array $dados): Voluntario
    {
        // Validar pessoa existe
        if (!Pessoa::find($dados['pessoa_id'])) {
            throw new ModelNotFoundException("Pessoa com ID {$dados['pessoa_id']} não encontrada.");
        }

        // Validar que pessoa não está vinculada a outro voluntário
        if ($this->buscarPorPessoa($dados['pessoa_id'])) {
            throw new \InvalidArgumentException("Esta pessoa já está cadastrada como voluntário.");
        }

        // Status padrão
        $dados['status'] = $dados['status'] ?? 'pendente';

        return Voluntario::create($dados);
    }

    /**
     * Atualiza um voluntário existente
     */
    public function atualizar(int $id, array $dados): Voluntario
    {
        $voluntario = $this->buscarPorId($id);

        // Validar pessoa existe se estiver alterando
        if (isset($dados['pessoa_id']) && $dados['pessoa_id'] !== $voluntario->pessoa_id) {
            if (!Pessoa::find($dados['pessoa_id'])) {
                throw new ModelNotFoundException("Pessoa com ID {$dados['pessoa_id']} não encontrada.");
            }

            // Validar que nova pessoa não está vinculada a outro voluntário
            if ($this->buscarPorPessoa($dados['pessoa_id'])) {
                throw new \InvalidArgumentException("Esta pessoa já está cadastrada como voluntário.");
            }
        }

        $voluntario->update($dados);
        return $voluntario->fresh();
    }

    /**
     * Aprova um voluntário
     */
    public function aprovar(int $id): Voluntario
    {
        $voluntario = $this->buscarPorId($id);
        $voluntario->update([
            'status' => 'aprovado',
            'dt_aprovacao' => now()
        ]);
        return $voluntario->fresh();
    }

    /**
     * Rejeita um voluntário
     */
    public function rejeitar(int $id, string $obs = null): Voluntario
    {
        $voluntario = $this->buscarPorId($id);
        $dados = ['status' => 'rejeitado'];
        
        if ($obs) {
            $dados['obs'] = $obs;
        }

        $voluntario->update($dados);
        return $voluntario->fresh();
    }

    /**
     * Exclui um voluntário
     */
    public function excluir(int $id): bool
    {
        $voluntario = $this->buscarPorId($id);

        // Verificar se há candidaturas vinculadas
        if ($voluntario->candidaturas()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir o voluntário. Existem candidaturas vinculadas.");
        }

        return $voluntario->delete();
    }
}


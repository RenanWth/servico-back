<?php

namespace App\Services;

use App\Models\Pessoa;
use App\Models\Perfil;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PessoaService
{
    /**
     * Lista todas as pessoas
     */
    public function listar(array $filtros = []): Collection
    {
        $query = Pessoa::query();

        if (isset($filtros['ativo'])) {
            $query->where('ativo', $filtros['ativo']);
        }

        if (isset($filtros['perfil_id'])) {
            $query->where('perfil_id', $filtros['perfil_id']);
        }

        return $query->get();
    }

    /**
     * Lista apenas pessoas ativas
     */
    public function listarAtivas(): Collection
    {
        return Pessoa::where('ativo', true)->get();
    }

    /**
     * Busca pessoa por ID
     */
    public function buscarPorId(int $id): Pessoa
    {
        $pessoa = Pessoa::find($id);
        
        if (!$pessoa) {
            throw new ModelNotFoundException("Pessoa com ID {$id} não encontrada.");
        }
        
        return $pessoa;
    }

    /**
     * Busca pessoa por CPF
     */
    public function buscarPorCpf(string $cpf): ?Pessoa
    {
        return Pessoa::where('cpf', $cpf)->first();
    }

    /**
     * Busca pessoa por email
     */
    public function buscarPorEmail(string $email): ?Pessoa
    {
        return Pessoa::where('email', $email)->first();
    }

    /**
     * Cria uma nova pessoa
     */
    public function criar(array $dados): Pessoa
    {
        // Validar perfil existe
        if (!Perfil::find($dados['perfil_id'])) {
            throw new ModelNotFoundException("Perfil com ID {$dados['perfil_id']} não encontrado.");
        }

        // Validar CPF único se informado
        if (isset($dados['cpf']) && !empty($dados['cpf'])) {
            if ($this->buscarPorCpf($dados['cpf'])) {
                throw new \InvalidArgumentException("Já existe uma pessoa com o CPF '{$dados['cpf']}'.");
            }
        }

        // Validar email único se informado
        if (isset($dados['email']) && !empty($dados['email'])) {
            if ($this->buscarPorEmail($dados['email'])) {
                throw new \InvalidArgumentException("Já existe uma pessoa com o email '{$dados['email']}'.");
            }
        }

        // Validar data de nascimento não pode ser futura
        if (isset($dados['dt_nascimento']) && $dados['dt_nascimento'] > now()) {
            throw new \InvalidArgumentException("Data de nascimento não pode ser futura.");
        }

        // Valores padrão
        $dados['ativo'] = $dados['ativo'] ?? true;
        $dados['dt_cadastro'] = now();
        $dados['dt_atualizacao'] = now();

        return Pessoa::create($dados);
    }

    /**
     * Atualiza uma pessoa existente
     */
    public function atualizar(int $id, array $dados): Pessoa
    {
        $pessoa = $this->buscarPorId($id);

        // Validar perfil existe se estiver alterando
        if (isset($dados['perfil_id']) && !Perfil::find($dados['perfil_id'])) {
            throw new ModelNotFoundException("Perfil com ID {$dados['perfil_id']} não encontrado.");
        }

        // Validar CPF único se estiver alterando
        if (isset($dados['cpf']) && $dados['cpf'] !== $pessoa->cpf) {
            if ($this->buscarPorCpf($dados['cpf'])) {
                throw new \InvalidArgumentException("Já existe uma pessoa com o CPF '{$dados['cpf']}'.");
            }
        }

        // Validar email único se estiver alterando
        if (isset($dados['email']) && $dados['email'] !== $pessoa->email) {
            if ($this->buscarPorEmail($dados['email'])) {
                throw new \InvalidArgumentException("Já existe uma pessoa com o email '{$dados['email']}'.");
            }
        }

        // Validar data de nascimento não pode ser futura
        if (isset($dados['dt_nascimento']) && $dados['dt_nascimento'] > now()) {
            throw new \InvalidArgumentException("Data de nascimento não pode ser futura.");
        }

        $dados['dt_atualizacao'] = now();
        $pessoa->update($dados);
        return $pessoa->fresh();
    }

    /**
     * Ativa uma pessoa
     */
    public function ativar(int $id): Pessoa
    {
        $pessoa = $this->buscarPorId($id);
        $pessoa->update(['ativo' => true, 'dt_atualizacao' => now()]);
        return $pessoa->fresh();
    }

    /**
     * Desativa uma pessoa
     */
    public function desativar(int $id): Pessoa
    {
        $pessoa = $this->buscarPorId($id);
        $pessoa->update(['ativo' => false, 'dt_atualizacao' => now()]);
        return $pessoa->fresh();
    }

    /**
     * Exclui uma pessoa
     */
    public function excluir(int $id): bool
    {
        $pessoa = $this->buscarPorId($id);

        // Verificar relacionamentos
        if ($pessoa->doacoes()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir a pessoa. Existem doações vinculadas.");
        }

        if ($pessoa->missoesCriadas()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir a pessoa. Existem missões criadas por ela.");
        }

        if ($pessoa->noticiasCriadas()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir a pessoa. Existem notícias criadas por ela.");
        }

        if ($pessoa->pontosColetaCriados()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir a pessoa. Existem pontos de coleta criados por ela.");
        }

        return $pessoa->delete();
    }
}


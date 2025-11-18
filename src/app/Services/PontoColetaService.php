<?php

namespace App\Services;

use App\Models\PontoColeta;
use App\Models\Pessoa;
use App\Models\Cidade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PontoColetaService
{
    /**
     * Lista todos os pontos de coleta
     */
    public function listar(): Collection
    {
        return PontoColeta::with(['cidade', 'adminCriador'])->get();
    }

    /**
     * Lista apenas pontos de coleta ativos
     */
    public function listarAtivos(): Collection
    {
        return PontoColeta::where('ativo', true)
            ->with(['cidade', 'adminCriador'])
            ->get();
    }

    /**
     * Lista pontos por cidade
     */
    public function listarPorCidade(int $cidadeId): Collection
    {
        return PontoColeta::where('cidades_id', $cidadeId)
            ->with(['cidade', 'adminCriador'])
            ->get();
    }

    /**
     * Busca ponto de coleta por ID
     */
    public function buscarPorId(int $id): PontoColeta
    {
        $ponto = PontoColeta::with(['cidade', 'adminCriador', 'necessidades', 'doacoes'])->find($id);
        
        if (!$ponto) {
            throw new ModelNotFoundException("Ponto de coleta com ID {$id} não encontrado.");
        }
        
        return $ponto;
    }

    /**
     * Cria um novo ponto de coleta
     */
    public function criar(array $dados, int $adminId): PontoColeta
    {
        // Cidade sempre ID 1
        $dados['cidades_id'] = 1;

        // Validar cidade existe
        if (!Cidade::find(1)) {
            throw new ModelNotFoundException("Cidade com ID 1 não encontrada.");
        }

        // Validar admin existe e tem perfil ADMIN
        $admin = Pessoa::find($adminId);
        if (!$admin) {
            throw new ModelNotFoundException("Admin com ID {$adminId} não encontrado.");
        }

        if ($admin->perfil->nome !== 'ADMIN') {
            throw new \InvalidArgumentException("Apenas administradores podem criar pontos de coleta.");
        }

        // Valores padrão
        $dados['admin_criador_id'] = $adminId;
        $dados['ativo'] = $dados['ativo'] ?? true;
        $dados['dt_criacao'] = now();

        return PontoColeta::create($dados);
    }

    /**
     * Atualiza um ponto de coleta existente
     */
    public function atualizar(int $id, array $dados): PontoColeta
    {
        $ponto = $this->buscarPorId($id);

        // Cidade sempre ID 1
        if (isset($dados['cidades_id'])) {
            $dados['cidades_id'] = 1;
        }

        $ponto->update($dados);
        return $ponto->fresh();
    }

    /**
     * Ativa um ponto de coleta
     */
    public function ativar(int $id): PontoColeta
    {
        $ponto = $this->buscarPorId($id);
        $ponto->update(['ativo' => true]);
        return $ponto->fresh();
    }

    /**
     * Desativa um ponto de coleta
     */
    public function desativar(int $id): PontoColeta
    {
        $ponto = $this->buscarPorId($id);
        $ponto->update(['ativo' => false]);
        return $ponto->fresh();
    }

    /**
     * Exclui um ponto de coleta
     */
    public function excluir(int $id): bool
    {
        $ponto = $this->buscarPorId($id);

        // Verificar se há necessidades vinculadas
        if ($ponto->necessidades()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir o ponto de coleta. Existem necessidades vinculadas.");
        }

        // Verificar se há doações vinculadas
        if ($ponto->doacoes()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir o ponto de coleta. Existem doações vinculadas.");
        }

        return $ponto->delete();
    }
}


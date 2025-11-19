<?php

namespace App\Services;

use App\Models\Missao;
use App\Models\CategoriaMissao;
use App\Models\Pessoa;
use App\Models\Cidade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MissaoService
{
    /**
     * Lista todas as missões
     */
    public function listar(array $filtros = []): Collection
    {
        $query = Missao::with(['categoria', 'cidade', 'adminCriador']);

        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (isset($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (isset($filtros['cidades_id'])) {
            $query->where('cidades_id', $filtros['cidades_id']);
        }

        return $query->get();
    }

    /**
     * Lista missões por status
     */
    public function listarPorStatus(string $status): Collection
    {
        return Missao::where('status', $status)
            ->with(['categoria', 'cidade', 'adminCriador'])
            ->get();
    }

    /**
     * Lista missões por categoria
     */
    public function listarPorCategoria(int $categoriaId): Collection
    {
        return Missao::where('categoria_id', $categoriaId)
            ->with(['categoria', 'cidade', 'adminCriador'])
            ->get();
    }

    /**
     * Lista missões por cidade
     */
    public function listarPorCidade(int $cidadeId): Collection
    {
        return Missao::where('cidades_id', $cidadeId)
            ->with(['categoria', 'cidade', 'adminCriador'])
            ->get();
    }

    /**
     * Lista missões com vagas disponíveis
     */
    public function listarDisponiveis(): Collection
    {
        return Missao::whereColumn('vagas_preenchidas', '<', 'vagas_totais')
            ->where('status', 'ativa')
            ->with(['categoria', 'cidade', 'adminCriador'])
            ->get();
    }

    /**
     * Busca missão por ID
     */
    public function buscarPorId(int $id): Missao
    {
        $missao = Missao::with(['categoria', 'cidade', 'adminCriador', 'candidaturas'])->find($id);
        
        if (!$missao) {
            throw new ModelNotFoundException("Missão com ID {$id} não encontrada.");
        }
        
        return $missao;
    }

    /**
     * Cria uma nova missão
     */
    public function criar(array $dados, int $adminId): Missao
    {
        // Validar categoria existe
        if (!CategoriaMissao::find($dados['categoria_id'])) {
            throw new ModelNotFoundException("Categoria com ID {$dados['categoria_id']} não encontrada.");
        }

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
            throw new \InvalidArgumentException("Apenas administradores podem criar missões.");
        }

        // Validar dt_inicio não pode ser anterior à data atual
        if (isset($dados['dt_inicio']) && $dados['dt_inicio'] < now()) {
            throw new \InvalidArgumentException("Data de início não pode ser anterior à data atual.");
        }

        // Validar dt_fim não pode ser anterior a dt_inicio
        if (isset($dados['dt_inicio']) && isset($dados['dt_fim']) && $dados['dt_fim'] < $dados['dt_inicio']) {
            throw new \InvalidArgumentException("Data de fim não pode ser anterior à data de início.");
        }

        // Validar vagas_totais > 0
        if (isset($dados['vagas_totais']) && $dados['vagas_totais'] <= 0) {
            throw new \InvalidArgumentException("Vagas totais deve ser maior que zero.");
        }

        // Valores padrão
        $dados['admin_criador_id'] = $adminId;
        $dados['vagas_preenchidas'] = $dados['vagas_preenchidas'] ?? 0;
        $dados['status'] = $dados['status'] ?? 'ativa';
        $dados['dt_criacao'] = now();
        $dados['dt_atualizacao'] = now();

        return Missao::create($dados);
    }

    /**
     * Atualiza uma missão existente
     */
    public function atualizar(int $id, array $dados): Missao
    {
        $missao = $this->buscarPorId($id);

        // Validar categoria existe se estiver alterando
        if (isset($dados['categoria_id']) && !CategoriaMissao::find($dados['categoria_id'])) {
            throw new ModelNotFoundException("Categoria com ID {$dados['categoria_id']} não encontrada.");
        }

        // Cidade sempre ID 1
        if (isset($dados['cidades_id'])) {
            $dados['cidades_id'] = 1;
        }

        // Validar dt_inicio não pode ser anterior à data atual
        if (isset($dados['dt_inicio']) && $dados['dt_inicio'] < now()) {
            throw new \InvalidArgumentException("Data de início não pode ser anterior à data atual.");
        }

        // Validar dt_fim não pode ser anterior a dt_inicio
        $dtInicio = $dados['dt_inicio'] ?? $missao->dt_inicio;
        if (isset($dados['dt_fim']) && $dados['dt_fim'] < $dtInicio) {
            throw new \InvalidArgumentException("Data de fim não pode ser anterior à data de início.");
        }

        // Validar vagas_totais > 0
        if (isset($dados['vagas_totais']) && $dados['vagas_totais'] <= 0) {
            throw new \InvalidArgumentException("Vagas totais deve ser maior que zero.");
        }

        // Não permitir vagas_preenchidas > vagas_totais
        $vagasTotais = $dados['vagas_totais'] ?? $missao->vagas_totais;
        $vagasPreenchidas = $dados['vagas_preenchidas'] ?? $missao->vagas_preenchidas;
        if ($vagasPreenchidas > $vagasTotais) {
            throw new \InvalidArgumentException("Vagas preenchidas não pode ser maior que vagas totais.");
        }

        $dados['dt_atualizacao'] = now();
        $missao->update($dados);
        return $missao->fresh();
    }

    /**
     * Atualiza vagas preenchidas
     */
    public function atualizarVagas(int $id, int $vagasPreenchidas): Missao
    {
        $missao = $this->buscarPorId($id);

        if ($vagasPreenchidas > $missao->vagas_totais) {
            throw new \InvalidArgumentException("Vagas preenchidas não pode ser maior que vagas totais.");
        }

        $missao->update([
            'vagas_preenchidas' => $vagasPreenchidas,
            'dt_atualizacao' => now()
        ]);

        return $missao->fresh();
    }

    /**
     * Finaliza uma missão
     */
    public function finalizar(int $id): Missao
    {
        $missao = $this->buscarPorId($id);
        $missao->update([
            'status' => 'finalizada',
            'dt_atualizacao' => now()
        ]);
        return $missao->fresh();
    }

    /**
     * Cancela uma missão
     */
    public function cancelar(int $id): Missao
    {
        $missao = $this->buscarPorId($id);
        $missao->update([
            'status' => 'cancelada',
            'dt_atualizacao' => now()
        ]);
        return $missao->fresh();
    }

    /**
     * Exclui uma missão
     */
    public function excluir(int $id): bool
    {
        $missao = $this->buscarPorId($id);

        // Verificar se há candidaturas vinculadas
        if ($missao->candidaturas()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir a missão. Existem candidaturas vinculadas.");
        }

        return $missao->delete();
    }
}


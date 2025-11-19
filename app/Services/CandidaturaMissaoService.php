<?php

namespace App\Services;

use App\Models\CandidaturaMissao;
use App\Models\Missao;
use App\Models\Voluntario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CandidaturaMissaoService
{
    /**
     * Lista todas as candidaturas
     */
    public function listar(): Collection
    {
        return CandidaturaMissao::with(['missao', 'voluntario.pessoa'])->get();
    }

    /**
     * Lista candidaturas de uma missão
     */
    public function listarPorMissao(int $missaoId): Collection
    {
        return CandidaturaMissao::where('missao_id', $missaoId)
            ->with(['missao', 'voluntario.pessoa'])
            ->get();
    }

    /**
     * Lista candidaturas de um voluntário
     */
    public function listarPorVoluntario(int $voluntarioId): Collection
    {
        return CandidaturaMissao::where('voluntario_id', $voluntarioId)
            ->with(['missao', 'voluntario.pessoa'])
            ->get();
    }

    /**
     * Lista candidaturas por status
     */
    public function listarPorStatus(string $status): Collection
    {
        return CandidaturaMissao::where('status', $status)
            ->with(['missao', 'voluntario.pessoa'])
            ->get();
    }

    /**
     * Busca candidatura por ID
     */
    public function buscarPorId(int $id): CandidaturaMissao
    {
        $candidatura = CandidaturaMissao::with(['missao', 'voluntario.pessoa'])->find($id);
        
        if (!$candidatura) {
            throw new ModelNotFoundException("Candidatura com ID {$id} não encontrada.");
        }
        
        return $candidatura;
    }

    /**
     * Cria uma nova candidatura
     */
    public function criar(array $dados): CandidaturaMissao
    {
        // Validar missão existe
        $missao = Missao::find($dados['missao_id']);
        if (!$missao) {
            throw new ModelNotFoundException("Missão com ID {$dados['missao_id']} não encontrada.");
        }

        // Validar voluntário existe e é aprovado
        $voluntario = Voluntario::find($dados['voluntario_id']);
        if (!$voluntario) {
            throw new ModelNotFoundException("Voluntário com ID {$dados['voluntario_id']} não encontrado.");
        }

        if ($voluntario->status !== 'aprovado') {
            throw new \InvalidArgumentException("Apenas voluntários aprovados podem se candidatar.");
        }

        // Validar que voluntário não está duplicado na mesma missão
        $candidaturaExistente = CandidaturaMissao::where('missao_id', $dados['missao_id'])
            ->where('voluntario_id', $dados['voluntario_id'])
            ->first();

        if ($candidaturaExistente) {
            throw new \InvalidArgumentException("Este voluntário já se candidatou a esta missão.");
        }

        // Status padrão
        $dados['status'] = $dados['status'] ?? 'pendente';
        $dados['dt_candidatura'] = now();

        return CandidaturaMissao::create($dados);
    }

    /**
     * Atualiza uma candidatura existente
     */
    public function atualizar(int $id, array $dados): CandidaturaMissao
    {
        $candidatura = $this->buscarPorId($id);

        // Validar missão existe se estiver alterando
        if (isset($dados['missao_id']) && $dados['missao_id'] !== $candidatura->missao_id) {
            if (!Missao::find($dados['missao_id'])) {
                throw new ModelNotFoundException("Missão com ID {$dados['missao_id']} não encontrada.");
            }
        }

        // Validar voluntário existe se estiver alterando
        if (isset($dados['voluntario_id']) && $dados['voluntario_id'] !== $candidatura->voluntario_id) {
            $voluntario = Voluntario::find($dados['voluntario_id']);
            if (!$voluntario) {
                throw new ModelNotFoundException("Voluntário com ID {$dados['voluntario_id']} não encontrado.");
            }

            if ($voluntario->status !== 'aprovado') {
                throw new \InvalidArgumentException("Apenas voluntários aprovados podem se candidatar.");
            }
        }

        $candidatura->update($dados);
        return $candidatura->fresh();
    }

    /**
     * Aprova uma candidatura
     */
    public function aprovar(int $id): CandidaturaMissao
    {
        $candidatura = $this->buscarPorId($id);
        $missao = $candidatura->missao;

        // Validar que missão tem vagas disponíveis
        if ($missao->vagas_preenchidas >= $missao->vagas_totais) {
            throw new \RuntimeException("A missão não possui mais vagas disponíveis.");
        }

        $candidatura->update([
            'status' => 'aprovada',
            'dt_aprovacao' => now()
        ]);

        // Incrementar vagas preenchidas da missão
        $missao->increment('vagas_preenchidas');
        $missao->update(['dt_atualizacao' => now()]);

        return $candidatura->fresh();
    }

    /**
     * Rejeita uma candidatura
     */
    public function rejeitar(int $id, string $obs = null): CandidaturaMissao
    {
        $candidatura = $this->buscarPorId($id);
        $dados = [
            'status' => 'rejeitada'
        ];

        if ($obs) {
            $dados['obs_avaliacao'] = $obs;
        }

        $candidatura->update($dados);
        return $candidatura->fresh();
    }

    /**
     * Conclui uma candidatura com avaliação
     */
    public function concluir(int $id, int $avaliacao = null, string $obsAvaliacao = null): CandidaturaMissao
    {
        $candidatura = $this->buscarPorId($id);

        // Validar avaliação entre 1 e 5
        if ($avaliacao !== null && ($avaliacao < 1 || $avaliacao > 5)) {
            throw new \InvalidArgumentException("Avaliação deve ser entre 1 e 5.");
        }

        $dados = [
            'status' => 'concluida',
            'dt_conclusao' => now()
        ];

        if ($avaliacao !== null) {
            $dados['avaliacao'] = $avaliacao;
        }

        if ($obsAvaliacao) {
            $dados['obs_avaliacao'] = $obsAvaliacao;
        }

        $candidatura->update($dados);
        return $candidatura->fresh();
    }

    /**
     * Exclui uma candidatura
     */
    public function excluir(int $id): bool
    {
        $candidatura = $this->buscarPorId($id);

        // Se estava aprovada, decrementar vagas da missão
        if ($candidatura->status === 'aprovada') {
            $missao = $candidatura->missao;
            if ($missao->vagas_preenchidas > 0) {
                $missao->decrement('vagas_preenchidas');
                $missao->update(['dt_atualizacao' => now()]);
            }
        }

        return $candidatura->delete();
    }
}


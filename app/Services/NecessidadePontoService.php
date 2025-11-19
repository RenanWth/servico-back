<?php

namespace App\Services;

use App\Models\NecessidadePonto;
use App\Models\PontoColeta;
use App\Models\TipoItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NecessidadePontoService
{
    /**
     * Lista todas as necessidades
     */
    public function listar(): Collection
    {
        return NecessidadePonto::with(['pontoColeta', 'tipoItem'])->get();
    }

    /**
     * Lista necessidades de um ponto
     */
    public function listarPorPonto(int $pontoColetaId): Collection
    {
        return NecessidadePonto::where('ponto_coleta_id', $pontoColetaId)
            ->with(['pontoColeta', 'tipoItem'])
            ->get();
    }

    /**
     * Lista apenas necessidades ativas
     */
    public function listarAtivas(): Collection
    {
        return NecessidadePonto::where('ativo', true)
            ->with(['pontoColeta', 'tipoItem'])
            ->get();
    }

    /**
     * Lista necessidades por prioridade
     */
    public function listarPorPrioridade(string $prioridade): Collection
    {
        return NecessidadePonto::where('prioridade', $prioridade)
            ->with(['pontoColeta', 'tipoItem'])
            ->get();
    }

    /**
     * Busca necessidade por ID
     */
    public function buscarPorId(int $id): NecessidadePonto
    {
        $necessidade = NecessidadePonto::with(['pontoColeta', 'tipoItem'])->find($id);
        
        if (!$necessidade) {
            throw new ModelNotFoundException("Necessidade com ID {$id} não encontrada.");
        }
        
        return $necessidade;
    }

    /**
     * Cria uma nova necessidade
     */
    public function criar(array $dados): NecessidadePonto
    {
        // Validar ponto de coleta existe
        if (!PontoColeta::find($dados['ponto_coleta_id'])) {
            throw new ModelNotFoundException("Ponto de coleta com ID {$dados['ponto_coleta_id']} não encontrado.");
        }

        // Validar tipo de item existe
        if (!TipoItem::find($dados['tipo_item_id'])) {
            throw new ModelNotFoundException("Tipo de item com ID {$dados['tipo_item_id']} não encontrado.");
        }

        // Validar quantidade_necessaria > 0
        if (!isset($dados['quantidade_necessaria']) || $dados['quantidade_necessaria'] <= 0) {
            throw new \InvalidArgumentException("Quantidade necessária deve ser maior que zero.");
        }

        // Valores padrão
        $dados['quantidade_recebida'] = $dados['quantidade_recebida'] ?? 0;
        $dados['prioridade'] = $dados['prioridade'] ?? 'media';
        $dados['ativo'] = $dados['ativo'] ?? true;
        $dados['dt_criacao'] = now();
        $dados['dt_atualizacao'] = now();

        return NecessidadePonto::create($dados);
    }

    /**
     * Atualiza uma necessidade existente
     */
    public function atualizar(int $id, array $dados): NecessidadePonto
    {
        $necessidade = $this->buscarPorId($id);

        // Validar ponto de coleta existe se estiver alterando
        if (isset($dados['ponto_coleta_id']) && !PontoColeta::find($dados['ponto_coleta_id'])) {
            throw new ModelNotFoundException("Ponto de coleta com ID {$dados['ponto_coleta_id']} não encontrado.");
        }

        // Validar tipo de item existe se estiver alterando
        if (isset($dados['tipo_item_id']) && !TipoItem::find($dados['tipo_item_id'])) {
            throw new ModelNotFoundException("Tipo de item com ID {$dados['tipo_item_id']} não encontrado.");
        }

        // Validar quantidade_necessaria > 0
        $quantidadeNecessaria = $dados['quantidade_necessaria'] ?? $necessidade->quantidade_necessaria;
        if ($quantidadeNecessaria <= 0) {
            throw new \InvalidArgumentException("Quantidade necessária deve ser maior que zero.");
        }

        // Não permitir quantidade_recebida > quantidade_necessaria
        $quantidadeRecebida = $dados['quantidade_recebida'] ?? $necessidade->quantidade_recebida;
        if ($quantidadeRecebida > $quantidadeNecessaria) {
            throw new \InvalidArgumentException("Quantidade recebida não pode ser maior que quantidade necessária.");
        }

        $dados['dt_atualizacao'] = now();
        $necessidade->update($dados);
        return $necessidade->fresh();
    }

    /**
     * Atualiza quantidade recebida
     */
    public function atualizarQuantidadeRecebida(int $id, float $quantidade): NecessidadePonto
    {
        $necessidade = $this->buscarPorId($id);

        if ($quantidade > $necessidade->quantidade_necessaria) {
            throw new \InvalidArgumentException("Quantidade recebida não pode ser maior que quantidade necessária.");
        }

        $necessidade->update([
            'quantidade_recebida' => $quantidade,
            'dt_atualizacao' => now()
        ]);

        return $necessidade->fresh();
    }

    /**
     * Ativa uma necessidade
     */
    public function ativar(int $id): NecessidadePonto
    {
        $necessidade = $this->buscarPorId($id);
        $necessidade->update(['ativo' => true, 'dt_atualizacao' => now()]);
        return $necessidade->fresh();
    }

    /**
     * Desativa uma necessidade
     */
    public function desativar(int $id): NecessidadePonto
    {
        $necessidade = $this->buscarPorId($id);
        $necessidade->update(['ativo' => false, 'dt_atualizacao' => now()]);
        return $necessidade->fresh();
    }

    /**
     * Exclui uma necessidade
     */
    public function excluir(int $id): bool
    {
        $necessidade = $this->buscarPorId($id);
        return $necessidade->delete();
    }
}


<?php

namespace App\Services;

use App\Models\Doacao;
use App\Models\Pessoa;
use App\Models\PontoColeta;
use App\Models\ItemDoacao;
use App\Models\NecessidadePonto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class DoacaoService
{
    /**
     * Lista todas as doações
     */
    public function listar(): Collection
    {
        return Doacao::with(['pessoa', 'pontoColeta', 'itens.tipoItem'])->get();
    }

    /**
     * Lista doações de uma pessoa
     */
    public function listarPorPessoa(int $pessoaId): Collection
    {
        return Doacao::where('pessoa_id', $pessoaId)
            ->with(['pessoa', 'pontoColeta', 'itens.tipoItem'])
            ->get();
    }

    /**
     * Lista doações de um ponto
     */
    public function listarPorPonto(int $pontoColetaId): Collection
    {
        return Doacao::where('ponto_coleta_id', $pontoColetaId)
            ->with(['pessoa', 'pontoColeta', 'itens.tipoItem'])
            ->get();
    }

    /**
     * Lista doações por status
     */
    public function listarPorStatus(string $status): Collection
    {
        return Doacao::where('status', $status)
            ->with(['pessoa', 'pontoColeta', 'itens.tipoItem'])
            ->get();
    }

    /**
     * Busca doação por ID
     */
    public function buscarPorId(int $id): Doacao
    {
        $doacao = Doacao::with(['pessoa', 'pontoColeta', 'itens.tipoItem'])->find($id);
        
        if (!$doacao) {
            throw new ModelNotFoundException("Doação com ID {$id} não encontrada.");
        }
        
        return $doacao;
    }

    /**
     * Cria uma nova doação com itens
     */
    public function criar(array $dados, array $itens): Doacao
    {
        // Validar pessoa existe
        if (!Pessoa::find($dados['pessoa_id'])) {
            throw new ModelNotFoundException("Pessoa com ID {$dados['pessoa_id']} não encontrada.");
        }

        // Validar ponto de coleta existe e está ativo
        $ponto = PontoColeta::find($dados['ponto_coleta_id']);
        if (!$ponto) {
            throw new ModelNotFoundException("Ponto de coleta com ID {$dados['ponto_coleta_id']} não encontrado.");
        }

        if (!$ponto->ativo) {
            throw new \InvalidArgumentException("O ponto de coleta não está ativo.");
        }

        // Validar que há pelo menos um item
        if (empty($itens) || count($itens) === 0) {
            throw new \InvalidArgumentException("A doação deve ter pelo menos um item.");
        }

        // Valores padrão
        $dados['dt_doacao'] = now();
        $dados['status'] = $dados['status'] ?? 'pendente';

        return DB::transaction(function () use ($dados, $itens) {
            $doacao = Doacao::create($dados);

            // Criar itens
            foreach ($itens as $item) {
                // Validar tipo de item existe
                if (!\App\Models\TipoItem::find($item['tipo_item_id'])) {
                    throw new ModelNotFoundException("Tipo de item com ID {$item['tipo_item_id']} não encontrado.");
                }

                // Validar quantidade > 0
                if (!isset($item['quantidade']) || $item['quantidade'] <= 0) {
                    throw new \InvalidArgumentException("Quantidade deve ser maior que zero.");
                }

                ItemDoacao::create([
                    'doacao_id' => $doacao->id,
                    'tipo_item_id' => $item['tipo_item_id'],
                    'quantidade' => $item['quantidade'],
                    'obs' => $item['obs'] ?? null,
                ]);
            }

            return $doacao->load(['pessoa', 'pontoColeta', 'itens.tipoItem']);
        });
    }

    /**
     * Atualiza uma doação existente
     */
    public function atualizar(int $id, array $dados): Doacao
    {
        $doacao = $this->buscarPorId($id);

        // Validar pessoa existe se estiver alterando
        if (isset($dados['pessoa_id']) && !Pessoa::find($dados['pessoa_id'])) {
            throw new ModelNotFoundException("Pessoa com ID {$dados['pessoa_id']} não encontrada.");
        }

        // Validar ponto de coleta existe se estiver alterando
        if (isset($dados['ponto_coleta_id'])) {
            $ponto = PontoColeta::find($dados['ponto_coleta_id']);
            if (!$ponto) {
                throw new ModelNotFoundException("Ponto de coleta com ID {$dados['ponto_coleta_id']} não encontrado.");
            }

            if (!$ponto->ativo) {
                throw new \InvalidArgumentException("O ponto de coleta não está ativo.");
            }
        }

        $doacao->update($dados);
        return $doacao->fresh(['pessoa', 'pontoColeta', 'itens.tipoItem']);
    }

    /**
     * Registra entrega da doação
     */
    public function registrarEntrega(int $id): Doacao
    {
        $doacao = $this->buscarPorId($id);

        if ($doacao->status === 'entregue') {
            throw new \RuntimeException("A doação já foi entregue.");
        }

        return DB::transaction(function () use ($doacao) {
            $doacao->update([
                'status' => 'entregue',
                'dt_entrega' => now()
            ]);

            // Atualizar quantidade recebida das necessidades
            foreach ($doacao->itens as $item) {
                $necessidades = NecessidadePonto::where('ponto_coleta_id', $doacao->ponto_coleta_id)
                    ->where('tipo_item_id', $item->tipo_item_id)
                    ->where('ativo', true)
                    ->get();

                foreach ($necessidades as $necessidade) {
                    $novaQuantidade = $necessidade->quantidade_recebida + $item->quantidade;
                    if ($novaQuantidade > $necessidade->quantidade_necessaria) {
                        $novaQuantidade = $necessidade->quantidade_necessaria;
                    }
                    $necessidade->update([
                        'quantidade_recebida' => $novaQuantidade,
                        'dt_atualizacao' => now()
                    ]);
                }
            }

            return $doacao->fresh(['pessoa', 'pontoColeta', 'itens.tipoItem']);
        });
    }

    /**
     * Cancela uma doação
     */
    public function cancelar(int $id): Doacao
    {
        $doacao = $this->buscarPorId($id);

        if ($doacao->status === 'entregue') {
            throw new \RuntimeException("Não é possível cancelar uma doação já entregue.");
        }

        $doacao->update(['status' => 'cancelada']);
        return $doacao->fresh(['pessoa', 'pontoColeta', 'itens.tipoItem']);
    }

    /**
     * Exclui uma doação
     */
    public function excluir(int $id): bool
    {
        $doacao = $this->buscarPorId($id);

        if ($doacao->status === 'entregue') {
            throw new \RuntimeException("Não é possível excluir uma doação já entregue.");
        }

        return $doacao->delete();
    }
}


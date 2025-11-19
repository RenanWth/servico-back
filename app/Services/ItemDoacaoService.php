<?php

namespace App\Services;

use App\Models\ItemDoacao;
use App\Models\Doacao;
use App\Models\TipoItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemDoacaoService
{
    /**
     * Lista itens de uma doação
     */
    public function listarPorDoacao(int $doacaoId): Collection
    {
        if (!Doacao::find($doacaoId)) {
            throw new ModelNotFoundException("Doação com ID {$doacaoId} não encontrada.");
        }

        return ItemDoacao::where('doacao_id', $doacaoId)
            ->with('tipoItem')
            ->get();
    }

    /**
     * Busca item por ID
     */
    public function buscarPorId(int $id): ItemDoacao
    {
        $item = ItemDoacao::with(['doacao', 'tipoItem'])->find($id);
        
        if (!$item) {
            throw new ModelNotFoundException("Item de doação com ID {$id} não encontrado.");
        }
        
        return $item;
    }

    /**
     * Cria um novo item de doação
     */
    public function criar(array $dados): ItemDoacao
    {
        // Validar doação existe
        if (!Doacao::find($dados['doacao_id'])) {
            throw new ModelNotFoundException("Doação com ID {$dados['doacao_id']} não encontrada.");
        }

        // Validar tipo de item existe
        if (!TipoItem::find($dados['tipo_item_id'])) {
            throw new ModelNotFoundException("Tipo de item com ID {$dados['tipo_item_id']} não encontrado.");
        }

        // Validar quantidade > 0
        if (!isset($dados['quantidade']) || $dados['quantidade'] <= 0) {
            throw new \InvalidArgumentException("Quantidade deve ser maior que zero.");
        }

        return ItemDoacao::create($dados);
    }

    /**
     * Atualiza um item de doação existente
     */
    public function atualizar(int $id, array $dados): ItemDoacao
    {
        $item = $this->buscarPorId($id);

        // Validar doação existe se estiver alterando
        if (isset($dados['doacao_id']) && !Doacao::find($dados['doacao_id'])) {
            throw new ModelNotFoundException("Doação com ID {$dados['doacao_id']} não encontrada.");
        }

        // Validar tipo de item existe se estiver alterando
        if (isset($dados['tipo_item_id']) && !TipoItem::find($dados['tipo_item_id'])) {
            throw new ModelNotFoundException("Tipo de item com ID {$dados['tipo_item_id']} não encontrado.");
        }

        // Validar quantidade > 0
        if (isset($dados['quantidade']) && $dados['quantidade'] <= 0) {
            throw new \InvalidArgumentException("Quantidade deve ser maior que zero.");
        }

        // Não permitir exclusão se doação já foi entregue
        if ($item->doacao->status === 'entregue') {
            throw new \RuntimeException("Não é possível alterar item de uma doação já entregue.");
        }

        $item->update($dados);
        return $item->fresh(['doacao', 'tipoItem']);
    }

    /**
     * Exclui um item de doação
     */
    public function excluir(int $id): bool
    {
        $item = $this->buscarPorId($id);

        // Não permitir exclusão se doação já foi entregue
        if ($item->doacao->status === 'entregue') {
            throw new \RuntimeException("Não é possível excluir item de uma doação já entregue.");
        }

        return $item->delete();
    }
}


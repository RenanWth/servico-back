<?php

namespace App\Services;

use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\Cidade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class EnderecoService
{
    /**
     * Lista endereços de uma pessoa
     */
    public function listarPorPessoa(int $pessoaId): Collection
    {
        if (!Pessoa::find($pessoaId)) {
            throw new ModelNotFoundException("Pessoa com ID {$pessoaId} não encontrada.");
        }

        return Endereco::where('pessoa_id', $pessoaId)->get();
    }

    /**
     * Busca endereço por ID
     */
    public function buscarPorId(int $id): Endereco
    {
        $endereco = Endereco::find($id);
        
        if (!$endereco) {
            throw new ModelNotFoundException("Endereço com ID {$id} não encontrado.");
        }
        
        return $endereco;
    }

    /**
     * Busca endereço principal de uma pessoa
     */
    public function buscarPrincipal(int $pessoaId): ?Endereco
    {
        if (!Pessoa::find($pessoaId)) {
            throw new ModelNotFoundException("Pessoa com ID {$pessoaId} não encontrada.");
        }

        return Endereco::where('pessoa_id', $pessoaId)
            ->where('principal', true)
            ->first();
    }

    /**
     * Cria um novo endereço
     */
    public function criar(array $dados): Endereco
    {
        // Validar pessoa existe
        if (!Pessoa::find($dados['pessoa_id'])) {
            throw new ModelNotFoundException("Pessoa com ID {$dados['pessoa_id']} não encontrada.");
        }

        // Cidade sempre ID 1
        $dados['cidades_id'] = 1;

        // Validar cidade existe
        if (!Cidade::find(1)) {
            throw new ModelNotFoundException("Cidade com ID 1 não encontrada.");
        }

        // Se for principal, desmarcar outros
        if (isset($dados['principal']) && $dados['principal']) {
            Endereco::where('pessoa_id', $dados['pessoa_id'])
                ->where('principal', true)
                ->update(['principal' => false]);
        }

        return Endereco::create($dados);
    }

    /**
     * Atualiza um endereço existente
     */
    public function atualizar(int $id, array $dados): Endereco
    {
        $endereco = $this->buscarPorId($id);

        // Cidade sempre ID 1
        if (isset($dados['cidades_id'])) {
            $dados['cidades_id'] = 1;
        }

        // Validar cidade existe
        if (!Cidade::find(1)) {
            throw new ModelNotFoundException("Cidade com ID 1 não encontrada.");
        }

        // Se for definir como principal, desmarcar outros
        if (isset($dados['principal']) && $dados['principal']) {
            Endereco::where('pessoa_id', $endereco->pessoa_id)
                ->where('id', '!=', $id)
                ->where('principal', true)
                ->update(['principal' => false]);
        }

        $endereco->update($dados);
        return $endereco->fresh();
    }

    /**
     * Define um endereço como principal
     */
    public function definirComoPrincipal(int $id): Endereco
    {
        $endereco = $this->buscarPorId($id);

        // Desmarcar outros endereços principais da mesma pessoa
        Endereco::where('pessoa_id', $endereco->pessoa_id)
            ->where('id', '!=', $id)
            ->where('principal', true)
            ->update(['principal' => false]);

        $endereco->update(['principal' => true]);
        return $endereco->fresh();
    }

    /**
     * Exclui um endereço
     */
    public function excluir(int $id): bool
    {
        $endereco = $this->buscarPorId($id);
        return $endereco->delete();
    }
}


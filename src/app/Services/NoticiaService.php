<?php

namespace App\Services;

use App\Models\Noticia;
use App\Models\CategoriaNoticia;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NoticiaService
{
    /**
     * Lista todas as notícias
     */
    public function listar(array $filtros = []): Collection
    {
        $query = Noticia::with(['categoria', 'adminAutor', 'imagens']);

        if (isset($filtros['status'])) {
            $query->where('status', $filtros['status']);
        }

        if (isset($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (isset($filtros['destaque'])) {
            $query->where('destaque', $filtros['destaque']);
        }

        return $query->get();
    }

    /**
     * Lista apenas notícias publicadas
     */
    public function listarPublicadas(): Collection
    {
        return Noticia::where('status', 'publicada')
            ->with(['categoria', 'adminAutor', 'imagens'])
            ->get();
    }

    /**
     * Lista notícias em destaque
     */
    public function listarDestaque(): Collection
    {
        return Noticia::where('destaque', true)
            ->where('status', 'publicada')
            ->with(['categoria', 'adminAutor', 'imagens'])
            ->get();
    }

    /**
     * Lista notícias por categoria
     */
    public function listarPorCategoria(int $categoriaId): Collection
    {
        return Noticia::where('categoria_id', $categoriaId)
            ->with(['categoria', 'adminAutor', 'imagens'])
            ->get();
    }

    /**
     * Lista notícias por status
     */
    public function listarPorStatus(string $status): Collection
    {
        return Noticia::where('status', $status)
            ->with(['categoria', 'adminAutor', 'imagens'])
            ->get();
    }

    /**
     * Busca notícia por ID
     */
    public function buscarPorId(int $id): Noticia
    {
        $noticia = Noticia::with(['categoria', 'adminAutor', 'imagens'])->find($id);
        
        if (!$noticia) {
            throw new ModelNotFoundException("Notícia com ID {$id} não encontrada.");
        }
        
        return $noticia;
    }

    /**
     * Cria uma nova notícia
     */
    public function criar(array $dados, int $adminId): Noticia
    {
        // Validar categoria existe
        if (!CategoriaNoticia::find($dados['categoria_id'])) {
            throw new ModelNotFoundException("Categoria com ID {$dados['categoria_id']} não encontrada.");
        }

        // Validar admin existe e tem perfil ADMIN
        $admin = Pessoa::find($adminId);
        if (!$admin) {
            throw new ModelNotFoundException("Admin com ID {$adminId} não encontrado.");
        }

        if ($admin->perfil->nome !== 'ADMIN') {
            throw new \InvalidArgumentException("Apenas administradores podem criar notícias.");
        }

        // Valores padrão
        $dados['admin_autor_id'] = $adminId;
        $dados['status'] = $dados['status'] ?? 'rascunho';
        $dados['destaque'] = $dados['destaque'] ?? false;
        $dados['visualizacoes'] = $dados['visualizacoes'] ?? 0;
        $dados['dt_atualizacao'] = now();

        return Noticia::create($dados);
    }

    /**
     * Atualiza uma notícia existente
     */
    public function atualizar(int $id, array $dados): Noticia
    {
        $noticia = $this->buscarPorId($id);

        // Validar categoria existe se estiver alterando
        if (isset($dados['categoria_id']) && !CategoriaNoticia::find($dados['categoria_id'])) {
            throw new ModelNotFoundException("Categoria com ID {$dados['categoria_id']} não encontrada.");
        }

        $dados['dt_atualizacao'] = now();
        $noticia->update($dados);
        return $noticia->fresh();
    }

    /**
     * Publica uma notícia
     */
    public function publicar(int $id): Noticia
    {
        $noticia = $this->buscarPorId($id);
        $noticia->update([
            'status' => 'publicada',
            'dt_publicacao' => now(),
            'dt_atualizacao' => now()
        ]);
        return $noticia->fresh();
    }

    /**
     * Define notícia como destaque
     */
    public function definirDestaque(int $id, bool $destaque): Noticia
    {
        $noticia = $this->buscarPorId($id);
        $noticia->update([
            'destaque' => $destaque,
            'dt_atualizacao' => now()
        ]);
        return $noticia->fresh();
    }

    /**
     * Incrementa contador de visualizações
     */
    public function incrementarVisualizacoes(int $id): Noticia
    {
        $noticia = $this->buscarPorId($id);
        $noticia->increment('visualizacoes');
        return $noticia->fresh();
    }

    /**
     * Exclui uma notícia
     */
    public function excluir(int $id): bool
    {
        $noticia = $this->buscarPorId($id);

        // Verificar se há imagens vinculadas
        if ($noticia->imagens()->count() > 0) {
            throw new \RuntimeException("Não é possível excluir a notícia. Existem imagens vinculadas.");
        }

        return $noticia->delete();
    }
}


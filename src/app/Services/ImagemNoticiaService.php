<?php

namespace App\Services;

use App\Models\ImagemNoticia;
use App\Models\Noticia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ImagemNoticiaService
{
    /**
     * Lista imagens de uma notícia
     */
    public function listarPorNoticia(int $noticiaId): Collection
    {
        if (!Noticia::find($noticiaId)) {
            throw new ModelNotFoundException("Notícia com ID {$noticiaId} não encontrada.");
        }

        return ImagemNoticia::where('noticia_id', $noticiaId)
            ->orderBy('ordem')
            ->get();
    }

    /**
     * Busca imagem por ID
     */
    public function buscarPorId(int $id): ImagemNoticia
    {
        $imagem = ImagemNoticia::find($id);
        
        if (!$imagem) {
            throw new ModelNotFoundException("Imagem com ID {$id} não encontrada.");
        }
        
        return $imagem;
    }

    /**
     * Busca imagem principal de uma notícia
     */
    public function buscarPrincipal(int $noticiaId): ?ImagemNoticia
    {
        if (!Noticia::find($noticiaId)) {
            throw new ModelNotFoundException("Notícia com ID {$noticiaId} não encontrada.");
        }

        return ImagemNoticia::where('noticia_id', $noticiaId)
            ->where('principal', true)
            ->first();
    }

    /**
     * Cria uma nova imagem
     */
    public function criar(array $dados): ImagemNoticia
    {
        // Validar notícia existe
        if (!Noticia::find($dados['noticia_id'])) {
            throw new ModelNotFoundException("Notícia com ID {$dados['noticia_id']} não encontrada.");
        }

        // Se for principal, desmarcar outras
        if (isset($dados['principal']) && $dados['principal']) {
            ImagemNoticia::where('noticia_id', $dados['noticia_id'])
                ->where('principal', true)
                ->update(['principal' => false]);
        }

        // Ordem padrão: próximo número disponível
        if (!isset($dados['ordem'])) {
            $ultimaOrdem = ImagemNoticia::where('noticia_id', $dados['noticia_id'])
                ->max('ordem') ?? 0;
            $dados['ordem'] = $ultimaOrdem + 1;
        }

        $dados['dt_upload'] = now();

        return ImagemNoticia::create($dados);
    }

    /**
     * Atualiza uma imagem existente
     */
    public function atualizar(int $id, array $dados): ImagemNoticia
    {
        $imagem = $this->buscarPorId($id);

        // Se for definir como principal, desmarcar outras
        if (isset($dados['principal']) && $dados['principal']) {
            ImagemNoticia::where('noticia_id', $imagem->noticia_id)
                ->where('id', '!=', $id)
                ->where('principal', true)
                ->update(['principal' => false]);
        }

        $imagem->update($dados);
        return $imagem->fresh();
    }

    /**
     * Define uma imagem como principal
     */
    public function definirComoPrincipal(int $id): ImagemNoticia
    {
        $imagem = $this->buscarPorId($id);

        // Desmarcar outras imagens principais da mesma notícia
        ImagemNoticia::where('noticia_id', $imagem->noticia_id)
            ->where('id', '!=', $id)
            ->where('principal', true)
            ->update(['principal' => false]);

        $imagem->update(['principal' => true]);
        return $imagem->fresh();
    }

    /**
     * Reordena imagens de uma notícia
     */
    public function reordenar(int $noticiaId, array $ordens): bool
    {
        if (!Noticia::find($noticiaId)) {
            throw new ModelNotFoundException("Notícia com ID {$noticiaId} não encontrada.");
        }

        foreach ($ordens as $imagemId => $ordem) {
            ImagemNoticia::where('id', $imagemId)
                ->where('noticia_id', $noticiaId)
                ->update(['ordem' => $ordem]);
        }

        return true;
    }

    /**
     * Exclui uma imagem
     */
    public function excluir(int $id): bool
    {
        $imagem = $this->buscarPorId($id);
        return $imagem->delete();
    }
}


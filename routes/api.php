<?php

use App\Http\Controllers\CandidaturaMissaoController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\ImagemNoticiaController;
use App\Http\Controllers\ItemDoacaoController;
use App\Http\Controllers\MissaoController;
use App\Http\Controllers\NecessidadePontoController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\PontoColetaController;
use App\Http\Controllers\VoluntarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API está funcionando!',
        'timestamp' => now()
    ]);
});

Route::middleware('verifyToken')->group(function () 
{
    // Perfis
    Route::apiResource('perfis', PerfilController::class);

    // Pessoas
    Route::apiResource('pessoas', PessoaController::class);
    Route::get('pessoas/ativas', [PessoaController::class, 'ativas']);
    Route::patch('pessoas/{id}/ativar', [PessoaController::class, 'ativar']);
    Route::patch('pessoas/{id}/desativar', [PessoaController::class, 'desativar']);

    // Endereços
    Route::get('pessoas/{pessoaId}/enderecos', [EnderecoController::class, 'index']);
    Route::apiResource('enderecos', EnderecoController::class)->except(['index']);
    Route::patch('enderecos/{id}/principal', [EnderecoController::class, 'definirPrincipal']);

    // Voluntários
    Route::apiResource('voluntarios', VoluntarioController::class);
    Route::get('voluntarios/status/{status}', [VoluntarioController::class, 'listarPorStatus']);
    Route::get('voluntarios/aprovados', [VoluntarioController::class, 'listarAprovados']);
    Route::patch('voluntarios/{id}/aprovar', [VoluntarioController::class, 'aprovar']);
    Route::patch('voluntarios/{id}/rejeitar', [VoluntarioController::class, 'rejeitar']);

    // Missões
    Route::apiResource('missoes', MissaoController::class);
    Route::get('missoes/status/{status}', [MissaoController::class, 'listarPorStatus']);
    Route::get('missoes/categoria/{categoriaId}', [MissaoController::class, 'listarPorCategoria']);
    Route::get('missoes/disponiveis', [MissaoController::class, 'listarDisponiveis']);
    Route::patch('missoes/{id}/finalizar', [MissaoController::class, 'finalizar']);
    Route::patch('missoes/{id}/cancelar', [MissaoController::class, 'cancelar']);

    // Candidaturas de Missão
    Route::apiResource('candidaturas-missao', CandidaturaMissaoController::class);
    Route::get('missoes/{missaoId}/candidaturas', [CandidaturaMissaoController::class, 'listarPorMissao']);
    Route::get('voluntarios/{voluntarioId}/candidaturas', [CandidaturaMissaoController::class, 'listarPorVoluntario']);
    Route::patch('candidaturas-missao/{id}/aprovar', [CandidaturaMissaoController::class, 'aprovar']);
    Route::patch('candidaturas-missao/{id}/rejeitar', [CandidaturaMissaoController::class, 'rejeitar']);
    Route::patch('candidaturas-missao/{id}/concluir', [CandidaturaMissaoController::class, 'concluir']);

    // Notícias
    Route::apiResource('noticias', NoticiaController::class);
    Route::get('noticias/publicadas', [NoticiaController::class, 'listarPublicadas']);
    Route::get('noticias/destaque', [NoticiaController::class, 'listarDestaque']);
    Route::get('noticias/categoria/{categoriaId}', [NoticiaController::class, 'listarPorCategoria']);
    Route::patch('noticias/{id}/publicar', [NoticiaController::class, 'publicar']);
    Route::patch('noticias/{id}/destaque', [NoticiaController::class, 'definirDestaque']);
    Route::patch('noticias/{id}/visualizacao', [NoticiaController::class, 'incrementarVisualizacoes']);

    // Imagens de Notícia
    Route::get('noticias/{noticiaId}/imagens', [ImagemNoticiaController::class, 'listarPorNoticia']);
    Route::apiResource('imagens-noticia', ImagemNoticiaController::class);
    Route::patch('imagens-noticia/{id}/principal', [ImagemNoticiaController::class, 'definirPrincipal']);
    Route::post('imagens-noticia/reordenar', [ImagemNoticiaController::class, 'reordenar']);

    // Pontos de Coleta
    Route::apiResource('pontos-coleta', PontoColetaController::class);
    Route::get('pontos-coleta/ativos', [PontoColetaController::class, 'listarAtivos']);
    Route::patch('pontos-coleta/{id}/ativar', [PontoColetaController::class, 'ativar']);
    Route::patch('pontos-coleta/{id}/desativar', [PontoColetaController::class, 'desativar']);

    // Necessidades de Ponto
    Route::get('pontos-coleta/{pontoId}/necessidades', [NecessidadePontoController::class, 'listarPorPonto']);
    Route::apiResource('necessidades-ponto', NecessidadePontoController::class);
    Route::get('necessidades-ponto/ativas', [NecessidadePontoController::class, 'listarAtivas']);
    Route::patch('necessidades-ponto/{id}/ativar', [NecessidadePontoController::class, 'ativar']);
    Route::patch('necessidades-ponto/{id}/desativar', [NecessidadePontoController::class, 'desativar']);

    // Doações
    Route::apiResource('doacoes', DoacaoController::class);
    Route::get('pessoas/{pessoaId}/doacoes', [DoacaoController::class, 'listarPorPessoa']);
    Route::get('pontos-coleta/{pontoId}/doacoes', [DoacaoController::class, 'listarPorPonto']);
    Route::patch('doacoes/{id}/entregar', [DoacaoController::class, 'registrarEntrega']);
    Route::patch('doacoes/{id}/cancelar', [DoacaoController::class, 'cancelar']);

    // Itens de Doação
    Route::get('tipos-item', [ItemDoacaoController::class, 'listarTiposItem']);
    Route::get('doacoes/{doacaoId}/itens', [ItemDoacaoController::class, 'listarPorDoacao']);
    Route::apiResource('itens-doacao', ItemDoacaoController::class);
});    
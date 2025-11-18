<?php

namespace App\Http\Controllers;

use App\Services\NoticiaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Notícias', description: 'Gerenciamento de notícias')]
class NoticiaController extends Controller
{
    public function __construct(
        private NoticiaService $noticiaService
    ) {}

    #[OA\Get(
        path: '/api/noticias',
        summary: 'Lista todas as notícias',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'categoria_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'destaque', in: 'query', schema: new OA\Schema(type: 'boolean'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de notícias',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['status', 'categoria_id', 'destaque']);
            $noticias = $this->noticiaService->listar($filtros);
            return response()->json(['data' => $noticias], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/noticias/publicadas',
        summary: 'Lista apenas notícias publicadas',
        tags: ['Notícias'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de notícias publicadas', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPublicadas(): JsonResponse
    {
        try {
            $noticias = $this->noticiaService->listarPublicadas();
            return response()->json(['data' => $noticias], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/noticias/destaque',
        summary: 'Lista notícias em destaque',
        tags: ['Notícias'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de notícias em destaque', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarDestaque(): JsonResponse
    {
        try {
            $noticias = $this->noticiaService->listarDestaque();
            return response()->json(['data' => $noticias], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/noticias/categoria/{categoriaId}',
        summary: 'Lista notícias por categoria',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'categoriaId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de notícias', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorCategoria(int $categoriaId): JsonResponse
    {
        try {
            $noticias = $this->noticiaService->listarPorCategoria($categoriaId);
            return response()->json(['data' => $noticias], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/noticias/{id}',
        summary: 'Busca notícia por ID',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Notícia encontrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Notícia não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $noticia = $this->noticiaService->buscarPorId($id);
            return response()->json(['data' => $noticia], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/noticias',
        summary: 'Cria uma nova notícia',
        tags: ['Notícias'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'conteudo', 'categoria_id', 'admin_id'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string', example: 'Título da notícia'),
                    new OA\Property(property: 'subtitulo', type: 'string', example: 'Subtítulo da notícia'),
                    new OA\Property(property: 'conteudo', type: 'string', example: 'Conteúdo completo da notícia'),
                    new OA\Property(property: 'categoria_id', type: 'integer', example: 1),
                    new OA\Property(property: 'destaque', type: 'boolean', example: false),
                    new OA\Property(property: 'status', type: 'string', example: 'rascunho'),
                    new OA\Property(property: 'admin_id', type: 'integer', example: 1, description: 'ID do administrador autor')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Notícia criada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:200',
                'subtitulo' => 'nullable|string',
                'conteudo' => 'required|string',
                'categoria_id' => 'required|exists:categorias_noticia,id',
                'destaque' => 'sometimes|boolean',
                'status' => 'sometimes|string',
                'admin_id' => 'required|exists:pessoas,id'
            ]);

            $adminId = $validated['admin_id'];
            unset($validated['admin_id']);

            $noticia = $this->noticiaService->criar($validated, $adminId);
            return response()->json(['data' => $noticia], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/noticias/{id}',
        summary: 'Atualiza uma notícia',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'subtitulo', type: 'string'),
                    new OA\Property(property: 'conteudo', type: 'string'),
                    new OA\Property(property: 'categoria_id', type: 'integer'),
                    new OA\Property(property: 'destaque', type: 'boolean'),
                    new OA\Property(property: 'status', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notícia atualizada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Notícia não encontrada')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'sometimes|string|max:200',
                'subtitulo' => 'nullable|string',
                'conteudo' => 'sometimes|string',
                'categoria_id' => 'sometimes|exists:categorias_noticia,id',
                'destaque' => 'sometimes|boolean',
                'status' => 'sometimes|string'
            ]);

            $noticia = $this->noticiaService->atualizar($id, $validated);
            return response()->json(['data' => $noticia], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/noticias/{id}/publicar',
        summary: 'Publica uma notícia',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Notícia publicada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Notícia não encontrada')
        ]
    )]
    public function publicar(int $id): JsonResponse
    {
        try {
            $noticia = $this->noticiaService->publicar($id);
            return response()->json(['data' => $noticia], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/noticias/{id}/destaque',
        summary: 'Define notícia como destaque',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['destaque'],
                properties: [
                    new OA\Property(property: 'destaque', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Destaque atualizado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Notícia não encontrada')
        ]
    )]
    public function definirDestaque(Request $request, int $id): JsonResponse
    {
        try {
            $destaque = $request->validate(['destaque' => 'required|boolean'])['destaque'];
            $noticia = $this->noticiaService->definirDestaque($id, $destaque);
            return response()->json(['data' => $noticia], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/noticias/{id}/visualizacao',
        summary: 'Incrementa contador de visualizações',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Visualização incrementada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Notícia não encontrada')
        ]
    )]
    public function incrementarVisualizacoes(int $id): JsonResponse
    {
        try {
            $noticia = $this->noticiaService->incrementarVisualizacoes($id);
            return response()->json(['data' => $noticia], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/noticias/{id}',
        summary: 'Exclui uma notícia',
        tags: ['Notícias'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Notícia excluída'),
            new OA\Response(response: 404, description: 'Notícia não encontrada'),
            new OA\Response(response: 409, description: 'Não é possível excluir, existem imagens vinculadas')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->noticiaService->excluir($id);
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

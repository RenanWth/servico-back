<?php

namespace App\Http\Controllers;

use App\Services\ImagemNoticiaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Imagens', description: 'Gerenciamento de imagens de notícias')]
class ImagemNoticiaController extends Controller
{
    public function __construct(
        private ImagemNoticiaService $imagemService
    ) {}

    #[OA\Get(
        path: '/api/noticias/{noticiaId}/imagens',
        summary: 'Lista imagens de uma notícia',
        tags: ['Imagens'],
        parameters: [
            new OA\Parameter(name: 'noticiaId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de imagens',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function listarPorNoticia(int $noticiaId): JsonResponse
    {
        try {
            $imagens = $this->imagemService->listarPorNoticia($noticiaId);
            return response()->json(['data' => $imagens], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/imagens-noticia/{id}',
        summary: 'Busca imagem por ID',
        tags: ['Imagens'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Imagem encontrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Imagem não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $imagem = $this->imagemService->buscarPorId($id);
            return response()->json(['data' => $imagem], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/imagens-noticia',
        summary: 'Cria uma nova imagem',
        tags: ['Imagens'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['noticia_id', 'url'],
                properties: [
                    new OA\Property(property: 'noticia_id', type: 'integer', example: 1),
                    new OA\Property(property: 'url', type: 'string', example: 'https://example.com/imagem.jpg'),
                    new OA\Property(property: 'legenda', type: 'string', example: 'Legenda da imagem'),
                    new OA\Property(property: 'ordem', type: 'integer', example: 1),
                    new OA\Property(property: 'principal', type: 'boolean', example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Imagem criada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'noticia_id' => 'required|exists:noticias,id',
                'url' => 'required|string|url',
                'legenda' => 'nullable|string',
                'ordem' => 'nullable|integer',
                'principal' => 'sometimes|boolean'
            ]);

            $imagem = $this->imagemService->criar($validated);
            return response()->json(['data' => $imagem], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/imagens-noticia/{id}',
        summary: 'Atualiza uma imagem',
        tags: ['Imagens'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'url', type: 'string'),
                    new OA\Property(property: 'legenda', type: 'string'),
                    new OA\Property(property: 'ordem', type: 'integer'),
                    new OA\Property(property: 'principal', type: 'boolean')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Imagem atualizada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Imagem não encontrada')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'url' => 'sometimes|string|url',
                'legenda' => 'nullable|string',
                'ordem' => 'nullable|integer',
                'principal' => 'sometimes|boolean'
            ]);

            $imagem = $this->imagemService->atualizar($id, $validated);
            return response()->json(['data' => $imagem], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/imagens-noticia/{id}/principal',
        summary: 'Define imagem como principal',
        tags: ['Imagens'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Imagem definida como principal', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Imagem não encontrada')
        ]
    )]
    public function definirPrincipal(int $id): JsonResponse
    {
        try {
            $imagem = $this->imagemService->definirComoPrincipal($id);
            return response()->json(['data' => $imagem], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/imagens-noticia/reordenar',
        summary: 'Reordena imagens de uma notícia',
        tags: ['Imagens'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['noticia_id', 'ordens'],
                properties: [
                    new OA\Property(property: 'noticia_id', type: 'integer', example: 1),
                    new OA\Property(property: 'ordens', type: 'object', example: ['1' => 1, '2' => 2, '3' => 3])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Imagens reordenadas'),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function reordenar(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'noticia_id' => 'required|exists:noticias,id',
                'ordens' => 'required|array'
            ]);

            $this->imagemService->reordenar($validated['noticia_id'], $validated['ordens']);
            return response()->json(['message' => 'Imagens reordenadas com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/imagens-noticia/{id}',
        summary: 'Exclui uma imagem',
        tags: ['Imagens'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Imagem excluída'),
            new OA\Response(response: 404, description: 'Imagem não encontrada')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->imagemService->excluir($id);
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

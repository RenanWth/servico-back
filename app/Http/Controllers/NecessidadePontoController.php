<?php

namespace App\Http\Controllers;

use App\Services\NecessidadePontoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Necessidades', description: 'Gerenciamento de necessidades dos pontos de coleta')]
class NecessidadePontoController extends Controller
{
    public function __construct(
        private NecessidadePontoService $necessidadeService
    ) {}

    #[OA\Get(
        path: '/api/pontos-coleta/{pontoId}/necessidades',
        summary: 'Lista necessidades de um ponto',
        tags: ['Necessidades'],
        parameters: [
            new OA\Parameter(name: 'pontoId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de necessidades',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function listarPorPonto(int $pontoId): JsonResponse
    {
        try {
            $necessidades = $this->necessidadeService->listarPorPonto($pontoId);
            return response()->json(['data' => $necessidades], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/necessidades-ponto',
        summary: 'Lista todas as necessidades',
        tags: ['Necessidades'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de necessidades',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        try {
            $necessidades = $this->necessidadeService->listar();
            return response()->json(['data' => $necessidades], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/necessidades-ponto/ativas',
        summary: 'Lista apenas necessidades ativas',
        tags: ['Necessidades'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de necessidades ativas', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarAtivas(): JsonResponse
    {
        try {
            $necessidades = $this->necessidadeService->listarAtivas();
            return response()->json(['data' => $necessidades], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/necessidades-ponto/{id}',
        summary: 'Busca necessidade por ID',
        tags: ['Necessidades'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Necessidade encontrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Necessidade não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $necessidade = $this->necessidadeService->buscarPorId($id);
            return response()->json(['data' => $necessidade], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/necessidades-ponto',
        summary: 'Cria uma nova necessidade',
        tags: ['Necessidades'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ponto_coleta_id', 'tipo_item_id', 'quantidade_necessaria'],
                properties: [
                    new OA\Property(property: 'ponto_coleta_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tipo_item_id', type: 'integer', example: 1),
                    new OA\Property(property: 'quantidade_necessaria', type: 'number', format: 'float', example: 100.00),
                    new OA\Property(property: 'prioridade', type: 'string', example: 'alta', enum: ['baixa', 'media', 'alta']),
                    new OA\Property(property: 'ativo', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Necessidade criada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ponto_coleta_id' => 'required|exists:pontos_coleta,id',
                'tipo_item_id' => 'required|exists:tipos_item,id',
                'quantidade_necessaria' => 'required|numeric|min:0.01',
                'prioridade' => 'nullable|string|in:baixa,media,alta',
                'ativo' => 'sometimes|boolean'
            ]);

            $necessidade = $this->necessidadeService->criar($validated);
            return response()->json(['data' => $necessidade], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/necessidades-ponto/{id}',
        summary: 'Atualiza uma necessidade',
        tags: ['Necessidades'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'quantidade_necessaria', type: 'number'),
                    new OA\Property(property: 'quantidade_recebida', type: 'number'),
                    new OA\Property(property: 'prioridade', type: 'string'),
                    new OA\Property(property: 'ativo', type: 'boolean')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Necessidade atualizada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Necessidade não encontrada')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantidade_necessaria' => 'sometimes|numeric|min:0.01',
                'quantidade_recebida' => 'nullable|numeric|min:0',
                'prioridade' => 'nullable|string|in:baixa,media,alta',
                'ativo' => 'sometimes|boolean'
            ]);

            $necessidade = $this->necessidadeService->atualizar($id, $validated);
            return response()->json(['data' => $necessidade], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/necessidades-ponto/{id}/ativar',
        summary: 'Ativa uma necessidade',
        tags: ['Necessidades'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Necessidade ativada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Necessidade não encontrada')
        ]
    )]
    public function ativar(int $id): JsonResponse
    {
        try {
            $necessidade = $this->necessidadeService->ativar($id);
            return response()->json(['data' => $necessidade], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/necessidades-ponto/{id}/desativar',
        summary: 'Desativa uma necessidade',
        tags: ['Necessidades'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Necessidade desativada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Necessidade não encontrada')
        ]
    )]
    public function desativar(int $id): JsonResponse
    {
        try {
            $necessidade = $this->necessidadeService->desativar($id);
            return response()->json(['data' => $necessidade], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/necessidades-ponto/{id}',
        summary: 'Exclui uma necessidade',
        tags: ['Necessidades'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Necessidade excluída'),
            new OA\Response(response: 404, description: 'Necessidade não encontrada')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->necessidadeService->excluir($id);
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ItemDoacaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Itens de Doação', description: 'Gerenciamento de itens de doação')]
class ItemDoacaoController extends Controller
{
    public function __construct(
        private ItemDoacaoService $itemService
    ) {}

    #[OA\Get(
        path: '/api/doacoes/{doacaoId}/itens',
        summary: 'Lista itens de uma doação',
        tags: ['Itens de Doação'],
        parameters: [
            new OA\Parameter(name: 'doacaoId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de itens',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function listarPorDoacao(int $doacaoId): JsonResponse
    {
        try {
            $itens = $this->itemService->listarPorDoacao($doacaoId);
            return response()->json(['data' => $itens], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/itens-doacao/{id}',
        summary: 'Busca item por ID',
        tags: ['Itens de Doação'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Item encontrado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Item não encontrado')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $item = $this->itemService->buscarPorId($id);
            return response()->json(['data' => $item], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/itens-doacao',
        summary: 'Cria um novo item de doação',
        tags: ['Itens de Doação'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['doacao_id', 'tipo_item_id', 'quantidade'],
                properties: [
                    new OA\Property(property: 'doacao_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tipo_item_id', type: 'integer', example: 1),
                    new OA\Property(property: 'quantidade', type: 'number', format: 'float', example: 10.50),
                    new OA\Property(property: 'obs', type: 'string', example: 'Observação do item')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Item criado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'doacao_id' => 'required|exists:doacoes,id',
                'tipo_item_id' => 'required|exists:tipos_item,id',
                'quantidade' => 'required|numeric|min:0.01',
                'obs' => 'nullable|string'
            ]);

            $item = $this->itemService->criar($validated);
            return response()->json(['data' => $item], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/itens-doacao/{id}',
        summary: 'Atualiza um item de doação',
        tags: ['Itens de Doação'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'tipo_item_id', type: 'integer'),
                    new OA\Property(property: 'quantidade', type: 'number'),
                    new OA\Property(property: 'obs', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Item atualizado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Item não encontrado'),
            new OA\Response(response: 409, description: 'Não é possível alterar, doação já foi entregue')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tipo_item_id' => 'sometimes|exists:tipos_item,id',
                'quantidade' => 'sometimes|numeric|min:0.01',
                'obs' => 'nullable|string'
            ]);

            $item = $this->itemService->atualizar($id, $validated);
            return response()->json(['data' => $item], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/itens-doacao/{id}',
        summary: 'Exclui um item de doação',
        tags: ['Itens de Doação'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Item excluído'),
            new OA\Response(response: 404, description: 'Item não encontrado'),
            new OA\Response(response: 409, description: 'Não é possível excluir, doação já foi entregue')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->itemService->excluir($id);
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

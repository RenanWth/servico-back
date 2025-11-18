<?php

namespace App\Http\Controllers;

use App\Services\DoacaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Doações', description: 'Gerenciamento de doações')]
class DoacaoController extends Controller
{
    public function __construct(
        private DoacaoService $doacaoService
    ) {}

    #[OA\Get(
        path: '/api/doacoes',
        summary: 'Lista todas as doações',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de doações',
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
            $filtros = $request->only(['status']);
            $doacoes = $this->doacaoService->listar();
            return response()->json(['data' => $doacoes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/pessoas/{pessoaId}/doacoes',
        summary: 'Lista doações de uma pessoa',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'pessoaId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de doações', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorPessoa(int $pessoaId): JsonResponse
    {
        try {
            $doacoes = $this->doacaoService->listarPorPessoa($pessoaId);
            return response()->json(['data' => $doacoes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/pontos-coleta/{pontoId}/doacoes',
        summary: 'Lista doações de um ponto',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'pontoId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de doações', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorPonto(int $pontoId): JsonResponse
    {
        try {
            $doacoes = $this->doacaoService->listarPorPonto($pontoId);
            return response()->json(['data' => $doacoes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/doacoes/{id}',
        summary: 'Busca doação por ID',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Doação encontrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Doação não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $doacao = $this->doacaoService->buscarPorId($id);
            return response()->json(['data' => $doacao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/doacoes',
        summary: 'Cria uma nova doação com itens',
        tags: ['Doações'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['pessoa_id', 'ponto_coleta_id', 'itens'],
                properties: [
                    new OA\Property(property: 'pessoa_id', type: 'integer', example: 1),
                    new OA\Property(property: 'ponto_coleta_id', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', example: 'pendente'),
                    new OA\Property(property: 'obs', type: 'string', example: 'Observações da doação'),
                    new OA\Property(
                        property: 'itens',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['tipo_item_id', 'quantidade'],
                            properties: [
                                new OA\Property(property: 'tipo_item_id', type: 'integer', example: 1),
                                new OA\Property(property: 'quantidade', type: 'number', format: 'float', example: 10.50),
                                new OA\Property(property: 'obs', type: 'string', example: 'Observação do item')
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Doação criada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'pessoa_id' => 'required|exists:pessoas,id',
                'ponto_coleta_id' => 'required|exists:pontos_coleta,id',
                'status' => 'sometimes|string',
                'obs' => 'nullable|string',
                'itens' => 'required|array|min:1',
                'itens.*.tipo_item_id' => 'required|exists:tipos_item,id',
                'itens.*.quantidade' => 'required|numeric|min:0.01',
                'itens.*.obs' => 'nullable|string'
            ]);

            $dados = $request->only(['pessoa_id', 'ponto_coleta_id', 'status', 'obs']);
            $itens = $request->input('itens');

            $doacao = $this->doacaoService->criar($dados, $itens);
            return response()->json(['data' => $doacao], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/doacoes/{id}',
        summary: 'Atualiza uma doação',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string'),
                    new OA\Property(property: 'obs', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Doação atualizada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Doação não encontrada')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'sometimes|string',
                'obs' => 'nullable|string'
            ]);

            $doacao = $this->doacaoService->atualizar($id, $validated);
            return response()->json(['data' => $doacao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/doacoes/{id}/entregar',
        summary: 'Registra entrega da doação',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Entrega registrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Doação não encontrada')
        ]
    )]
    public function registrarEntrega(int $id): JsonResponse
    {
        try {
            $doacao = $this->doacaoService->registrarEntrega($id);
            return response()->json(['data' => $doacao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/doacoes/{id}/cancelar',
        summary: 'Cancela uma doação',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Doação cancelada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Doação não encontrada'),
            new OA\Response(response: 409, description: 'Não é possível cancelar, doação já foi entregue')
        ]
    )]
    public function cancelar(int $id): JsonResponse
    {
        try {
            $doacao = $this->doacaoService->cancelar($id);
            return response()->json(['data' => $doacao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/doacoes/{id}',
        summary: 'Exclui uma doação',
        tags: ['Doações'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Doação excluída'),
            new OA\Response(response: 404, description: 'Doação não encontrada'),
            new OA\Response(response: 409, description: 'Não é possível excluir, doação já foi entregue')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->doacaoService->excluir($id);
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

<?php

namespace App\Http\Controllers;

use App\Services\PontoColetaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Pontos de Coleta', description: 'Gerenciamento de pontos de coleta')]
class PontoColetaController extends Controller
{
    public function __construct(
        private PontoColetaService $pontoService
    ) {}

    #[OA\Get(
        path: '/api/pontos-coleta',
        summary: 'Lista todos os pontos de coleta',
        tags: ['Pontos de Coleta'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de pontos de coleta',
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
            $pontos = $this->pontoService->listar();
            return response()->json(['data' => $pontos], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/pontos-coleta/ativos',
        summary: 'Lista apenas pontos de coleta ativos',
        tags: ['Pontos de Coleta'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de pontos ativos', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarAtivos(): JsonResponse
    {
        try {
            $pontos = $this->pontoService->listarAtivos();
            return response()->json(['data' => $pontos], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/pontos-coleta/{id}',
        summary: 'Busca ponto de coleta por ID',
        tags: ['Pontos de Coleta'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ponto encontrado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Ponto não encontrado')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $ponto = $this->pontoService->buscarPorId($id);
            return response()->json(['data' => $ponto], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/pontos-coleta',
        summary: 'Cria um novo ponto de coleta',
        tags: ['Pontos de Coleta'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome', 'admin_id'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Ponto de Coleta Central'),
                    new OA\Property(property: 'descricao', type: 'string', example: 'Descrição do ponto'),
                    new OA\Property(property: 'endereco', type: 'string', example: 'Rua Principal, 123'),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', example: -29.4669),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', example: -51.9644),
                    new OA\Property(property: 'telefone', type: 'string', example: '(51) 99999-9999'),
                    new OA\Property(property: 'horario_funcionamento', type: 'string', example: 'Segunda a Sexta, 8h às 18h'),
                    new OA\Property(property: 'responsavel_nome', type: 'string', example: 'João Silva'),
                    new OA\Property(property: 'responsavel_telefone', type: 'string', example: '(51) 88888-8888'),
                    new OA\Property(property: 'ativo', type: 'boolean', example: true),
                    new OA\Property(property: 'admin_id', type: 'integer', example: 1, description: 'ID do administrador criador')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ponto criado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:200',
                'descricao' => 'nullable|string',
                'endereco' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'telefone' => 'nullable|string|max:20',
                'horario_funcionamento' => 'nullable|string',
                'responsavel_nome' => 'nullable|string|max:200',
                'responsavel_telefone' => 'nullable|string|max:20',
                'ativo' => 'sometimes|boolean',
                'admin_id' => 'required|exists:pessoas,id'
            ]);

            $adminId = $validated['admin_id'];
            unset($validated['admin_id']);

            $ponto = $this->pontoService->criar($validated, $adminId);
            return response()->json(['data' => $ponto], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/pontos-coleta/{id}',
        summary: 'Atualiza um ponto de coleta',
        tags: ['Pontos de Coleta'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string'),
                    new OA\Property(property: 'descricao', type: 'string'),
                    new OA\Property(property: 'endereco', type: 'string'),
                    new OA\Property(property: 'latitude', type: 'number'),
                    new OA\Property(property: 'longitude', type: 'number'),
                    new OA\Property(property: 'telefone', type: 'string'),
                    new OA\Property(property: 'horario_funcionamento', type: 'string'),
                    new OA\Property(property: 'responsavel_nome', type: 'string'),
                    new OA\Property(property: 'responsavel_telefone', type: 'string'),
                    new OA\Property(property: 'ativo', type: 'boolean')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ponto atualizado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Ponto não encontrado')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nome' => 'sometimes|string|max:200',
                'descricao' => 'nullable|string',
                'endereco' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'telefone' => 'nullable|string|max:20',
                'horario_funcionamento' => 'nullable|string',
                'responsavel_nome' => 'nullable|string|max:200',
                'responsavel_telefone' => 'nullable|string|max:20',
                'ativo' => 'sometimes|boolean'
            ]);

            $ponto = $this->pontoService->atualizar($id, $validated);
            return response()->json(['data' => $ponto], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/pontos-coleta/{id}/ativar',
        summary: 'Ativa um ponto de coleta',
        tags: ['Pontos de Coleta'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ponto ativado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Ponto não encontrado')
        ]
    )]
    public function ativar(int $id): JsonResponse
    {
        try {
            $ponto = $this->pontoService->ativar($id);
            return response()->json(['data' => $ponto], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/pontos-coleta/{id}/desativar',
        summary: 'Desativa um ponto de coleta',
        tags: ['Pontos de Coleta'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ponto desativado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Ponto não encontrado')
        ]
    )]
    public function desativar(int $id): JsonResponse
    {
        try {
            $ponto = $this->pontoService->desativar($id);
            return response()->json(['data' => $ponto], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/pontos-coleta/{id}',
        summary: 'Exclui um ponto de coleta',
        tags: ['Pontos de Coleta'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Ponto excluído'),
            new OA\Response(response: 404, description: 'Ponto não encontrado'),
            new OA\Response(response: 409, description: 'Não é possível excluir, existem relacionamentos')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->pontoService->excluir($id);
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

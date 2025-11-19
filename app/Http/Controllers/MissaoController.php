<?php

namespace App\Http\Controllers;

use App\Services\MissaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Missões', description: 'Gerenciamento de missões')]
class MissaoController extends Controller
{
    public function __construct(
        private MissaoService $missaoService
    ) {}

    #[OA\Get(
        path: '/api/missoes',
        summary: 'Lista todas as missões',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'categoria_id', in: 'query', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de missões',
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
            $filtros = $request->only(['status', 'categoria_id']);
            $missoes = $this->missaoService->listar($filtros);
            return response()->json(['data' => $missoes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/missoes/status/{status}',
        summary: 'Lista missões por status',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de missões', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorStatus(string $status): JsonResponse
    {
        try {
            $missoes = $this->missaoService->listarPorStatus($status);
            return response()->json(['data' => $missoes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/missoes/categoria/{categoriaId}',
        summary: 'Lista missões por categoria',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'categoriaId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de missões', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorCategoria(int $categoriaId): JsonResponse
    {
        try {
            $missoes = $this->missaoService->listarPorCategoria($categoriaId);
            return response()->json(['data' => $missoes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/missoes/disponiveis',
        summary: 'Lista missões com vagas disponíveis',
        tags: ['Missões'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de missões disponíveis', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarDisponiveis(): JsonResponse
    {
        try {
            $missoes = $this->missaoService->listarDisponiveis();
            return response()->json(['data' => $missoes], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/missoes/{id}',
        summary: 'Busca missão por ID',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Missão encontrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Missão não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $missao = $this->missaoService->buscarPorId($id);
            return response()->json(['data' => $missao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/missoes',
        summary: 'Cria uma nova missão',
        tags: ['Missões'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['titulo', 'descricao', 'categoria_id', 'dt_inicio', 'dt_fim', 'vagas_totais', 'admin_id'],
                properties: [
                    new OA\Property(property: 'titulo', type: 'string', example: 'Missão de Resgate'),
                    new OA\Property(property: 'descricao', type: 'string', example: 'Descrição da missão'),
                    new OA\Property(property: 'categoria_id', type: 'integer', example: 1),
                    new OA\Property(property: 'local_encontro', type: 'string', example: 'Praça Central'),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', example: -29.4669),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', example: -51.9644),
                    new OA\Property(property: 'dt_inicio', type: 'string', format: 'date-time', example: '2025-12-01 08:00:00'),
                    new OA\Property(property: 'dt_fim', type: 'string', format: 'date-time', example: '2025-12-01 18:00:00'),
                    new OA\Property(property: 'vagas_totais', type: 'integer', example: 10),
                    new OA\Property(property: 'status', type: 'string', example: 'ativa'),
                    new OA\Property(property: 'admin_id', type: 'integer', example: 1, description: 'ID do administrador criador')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Missão criada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'required|string|max:200',
                'descricao' => 'required|string',
                'categoria_id' => 'required|exists:categorias_missao,id',
                'local_encontro' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'dt_inicio' => 'required|date|after:now',
                'dt_fim' => 'required|date|after:dt_inicio',
                'vagas_totais' => 'required|integer|min:1',
                'status' => 'sometimes|string',
                'admin_id' => 'required|exists:pessoas,id'
            ]);

            $adminId = $validated['admin_id'];
            unset($validated['admin_id']);

            $missao = $this->missaoService->criar($validated, $adminId);
            return response()->json(['data' => $missao], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/missoes/{id}',
        summary: 'Atualiza uma missão',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'titulo', type: 'string'),
                    new OA\Property(property: 'descricao', type: 'string'),
                    new OA\Property(property: 'categoria_id', type: 'integer'),
                    new OA\Property(property: 'local_encontro', type: 'string'),
                    new OA\Property(property: 'latitude', type: 'number'),
                    new OA\Property(property: 'longitude', type: 'number'),
                    new OA\Property(property: 'dt_inicio', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'dt_fim', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'vagas_totais', type: 'integer'),
                    new OA\Property(property: 'status', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Missão atualizada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Missão não encontrada')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'titulo' => 'sometimes|string|max:200',
                'descricao' => 'sometimes|string',
                'categoria_id' => 'sometimes|exists:categorias_missao,id',
                'local_encontro' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'dt_inicio' => 'sometimes|date|after:now',
                'dt_fim' => 'sometimes|date|after:dt_inicio',
                'vagas_totais' => 'sometimes|integer|min:1',
                'status' => 'sometimes|string'
            ]);

            $missao = $this->missaoService->atualizar($id, $validated);
            return response()->json(['data' => $missao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/missoes/{id}/finalizar',
        summary: 'Finaliza uma missão',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Missão finalizada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Missão não encontrada')
        ]
    )]
    public function finalizar(int $id): JsonResponse
    {
        try {
            $missao = $this->missaoService->finalizar($id);
            return response()->json(['data' => $missao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/missoes/{id}/cancelar',
        summary: 'Cancela uma missão',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Missão cancelada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Missão não encontrada')
        ]
    )]
    public function cancelar(int $id): JsonResponse
    {
        try {
            $missao = $this->missaoService->cancelar($id);
            return response()->json(['data' => $missao], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/missoes/{id}',
        summary: 'Exclui uma missão',
        tags: ['Missões'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Missão excluída'),
            new OA\Response(response: 404, description: 'Missão não encontrada'),
            new OA\Response(response: 409, description: 'Não é possível excluir, existem candidaturas vinculadas')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->missaoService->excluir($id);
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

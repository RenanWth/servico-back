<?php

namespace App\Http\Controllers;

use App\Services\VoluntarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Voluntários', description: 'Gerenciamento de voluntários')]
class VoluntarioController extends Controller
{
    public function __construct(
        private VoluntarioService $voluntarioService
    ) {}

    #[OA\Get(
        path: '/api/voluntarios',
        summary: 'Lista todos os voluntários',
        tags: ['Voluntários'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de voluntários',
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
            $voluntarios = $this->voluntarioService->listar();
            return response()->json(['data' => $voluntarios], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/voluntarios/status/{status}',
        summary: 'Lista voluntários por status',
        tags: ['Voluntários'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'path', required: true, schema: new OA\Schema(type: 'string', enum: ['pendente', 'aprovado', 'rejeitado']))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de voluntários', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorStatus(string $status): JsonResponse
    {
        try {
            $voluntarios = $this->voluntarioService->listarPorStatus($status);
            return response()->json(['data' => $voluntarios], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/voluntarios/aprovados',
        summary: 'Lista apenas voluntários aprovados',
        tags: ['Voluntários'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de voluntários aprovados', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarAprovados(): JsonResponse
    {
        try {
            $voluntarios = $this->voluntarioService->listarAprovados();
            return response()->json(['data' => $voluntarios], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/voluntarios/{id}',
        summary: 'Busca voluntário por ID',
        tags: ['Voluntários'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Voluntário encontrado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Voluntário não encontrado')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $voluntario = $this->voluntarioService->buscarPorId($id);
            return response()->json(['data' => $voluntario], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/voluntarios',
        summary: 'Cria um novo voluntário',
        tags: ['Voluntários'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['pessoa_id'],
                properties: [
                    new OA\Property(property: 'pessoa_id', type: 'integer', example: 1),
                    new OA\Property(property: 'escolaridade', type: 'string', example: 'Superior completo'),
                    new OA\Property(property: 'profissao', type: 'string', example: 'Engenheiro'),
                    new OA\Property(property: 'habilidades', type: 'string', example: 'Primeiros socorros, Resgate'),
                    new OA\Property(property: 'disponibilidade', type: 'string', example: 'Finais de semana'),
                    new OA\Property(property: 'exp_emergencias', type: 'boolean', example: true),
                    new OA\Property(property: 'cnh_categoria', type: 'string', example: 'B'),
                    new OA\Property(property: 'possui_veiculo', type: 'boolean', example: true),
                    new OA\Property(property: 'obs', type: 'string', example: 'Observações adicionais')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Voluntário criado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'pessoa_id' => 'required|exists:pessoas,id|unique:voluntarios,pessoa_id',
                'escolaridade' => 'nullable|string',
                'profissao' => 'nullable|string',
                'habilidades' => 'nullable|string',
                'disponibilidade' => 'nullable|string',
                'exp_emergencias' => 'nullable|boolean',
                'cnh_categoria' => 'nullable|string',
                'possui_veiculo' => 'nullable|boolean',
                'obs' => 'nullable|string'
            ]);

            $voluntario = $this->voluntarioService->criar($validated);
            return response()->json(['data' => $voluntario], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/voluntarios/{id}',
        summary: 'Atualiza um voluntário',
        tags: ['Voluntários'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'escolaridade', type: 'string'),
                    new OA\Property(property: 'profissao', type: 'string'),
                    new OA\Property(property: 'habilidades', type: 'string'),
                    new OA\Property(property: 'disponibilidade', type: 'string'),
                    new OA\Property(property: 'exp_emergencias', type: 'boolean'),
                    new OA\Property(property: 'cnh_categoria', type: 'string'),
                    new OA\Property(property: 'possui_veiculo', type: 'boolean'),
                    new OA\Property(property: 'obs', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Voluntário atualizado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Voluntário não encontrado')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'escolaridade' => 'nullable|string',
                'profissao' => 'nullable|string',
                'habilidades' => 'nullable|string',
                'disponibilidade' => 'nullable|string',
                'exp_emergencias' => 'nullable|boolean',
                'cnh_categoria' => 'nullable|string',
                'possui_veiculo' => 'nullable|boolean',
                'obs' => 'nullable|string'
            ]);

            $voluntario = $this->voluntarioService->atualizar($id, $validated);
            return response()->json(['data' => $voluntario], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/voluntarios/{id}/aprovar',
        summary: 'Aprova um voluntário',
        tags: ['Voluntários'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Voluntário aprovado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Voluntário não encontrado')
        ]
    )]
    public function aprovar(int $id): JsonResponse
    {
        try {
            $voluntario = $this->voluntarioService->aprovar($id);
            return response()->json(['data' => $voluntario], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/voluntarios/{id}/rejeitar',
        summary: 'Rejeita um voluntário',
        tags: ['Voluntários'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'obs', type: 'string', example: 'Motivo da rejeição')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Voluntário rejeitado', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Voluntário não encontrado')
        ]
    )]
    public function rejeitar(Request $request, int $id): JsonResponse
    {
        try {
            $obs = $request->input('obs');
            $voluntario = $this->voluntarioService->rejeitar($id, $obs);
            return response()->json(['data' => $voluntario], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/voluntarios/{id}',
        summary: 'Exclui um voluntário',
        tags: ['Voluntários'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Voluntário excluído'),
            new OA\Response(response: 404, description: 'Voluntário não encontrado'),
            new OA\Response(response: 409, description: 'Não é possível excluir, existem candidaturas vinculadas')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->voluntarioService->excluir($id);
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

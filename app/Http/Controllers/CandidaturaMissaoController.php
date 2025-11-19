<?php

namespace App\Http\Controllers;

use App\Services\CandidaturaMissaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Candidaturas', description: 'Gerenciamento de candidaturas a missões')]
class CandidaturaMissaoController extends Controller
{
    public function __construct(
        private CandidaturaMissaoService $candidaturaService
    ) {}

    #[OA\Get(
        path: '/api/candidaturas-missao',
        summary: 'Lista todas as candidaturas',
        tags: ['Candidaturas'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de candidaturas',
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
            $candidaturas = $this->candidaturaService->listar();
            return response()->json(['data' => $candidaturas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/missoes/{missaoId}/candidaturas',
        summary: 'Lista candidaturas de uma missão',
        tags: ['Candidaturas'],
        parameters: [
            new OA\Parameter(name: 'missaoId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de candidaturas', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorMissao(int $missaoId): JsonResponse
    {
        try {
            $candidaturas = $this->candidaturaService->listarPorMissao($missaoId);
            return response()->json(['data' => $candidaturas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/voluntarios/{voluntarioId}/candidaturas',
        summary: 'Lista candidaturas de um voluntário',
        tags: ['Candidaturas'],
        parameters: [
            new OA\Parameter(name: 'voluntarioId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de candidaturas', content: new OA\JsonContent(type: 'object'))
        ]
    )]
    public function listarPorVoluntario(int $voluntarioId): JsonResponse
    {
        try {
            $candidaturas = $this->candidaturaService->listarPorVoluntario($voluntarioId);
            return response()->json(['data' => $candidaturas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/candidaturas-missao/{id}',
        summary: 'Busca candidatura por ID',
        tags: ['Candidaturas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Candidatura encontrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Candidatura não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $candidatura = $this->candidaturaService->buscarPorId($id);
            return response()->json(['data' => $candidatura], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/candidaturas-missao',
        summary: 'Cria uma nova candidatura',
        tags: ['Candidaturas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['missao_id', 'voluntario_id'],
                properties: [
                    new OA\Property(property: 'missao_id', type: 'integer', example: 1),
                    new OA\Property(property: 'voluntario_id', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Candidatura criada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'missao_id' => 'required|exists:missoes,id',
                'voluntario_id' => 'required|exists:voluntarios,id'
            ]);

            $candidatura = $this->candidaturaService->criar($validated);
            return response()->json(['data' => $candidatura], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/candidaturas-missao/{id}/aprovar',
        summary: 'Aprova uma candidatura',
        tags: ['Candidaturas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Candidatura aprovada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Candidatura não encontrada'),
            new OA\Response(response: 409, description: 'Missão não possui mais vagas disponíveis')
        ]
    )]
    public function aprovar(int $id): JsonResponse
    {
        try {
            $candidatura = $this->candidaturaService->aprovar($id);
            return response()->json(['data' => $candidatura], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/candidaturas-missao/{id}/rejeitar',
        summary: 'Rejeita uma candidatura',
        tags: ['Candidaturas'],
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
            new OA\Response(response: 200, description: 'Candidatura rejeitada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Candidatura não encontrada')
        ]
    )]
    public function rejeitar(Request $request, int $id): JsonResponse
    {
        try {
            $obs = $request->input('obs');
            $candidatura = $this->candidaturaService->rejeitar($id, $obs);
            return response()->json(['data' => $candidatura], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/candidaturas-missao/{id}/concluir',
        summary: 'Conclui uma candidatura com avaliação',
        tags: ['Candidaturas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'avaliacao', type: 'integer', example: 5, description: 'Avaliação de 1 a 5'),
                    new OA\Property(property: 'obs_avaliacao', type: 'string', example: 'Observações da avaliação')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Candidatura concluída', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Candidatura não encontrada')
        ]
    )]
    public function concluir(Request $request, int $id): JsonResponse
    {
        try {
            $avaliacao = $request->input('avaliacao');
            $obsAvaliacao = $request->input('obs_avaliacao');
            $candidatura = $this->candidaturaService->concluir($id, $avaliacao, $obsAvaliacao);
            return response()->json(['data' => $candidatura], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/candidaturas-missao/{id}',
        summary: 'Exclui uma candidatura',
        tags: ['Candidaturas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Candidatura excluída'),
            new OA\Response(response: 404, description: 'Candidatura não encontrada')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->candidaturaService->excluir($id);
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

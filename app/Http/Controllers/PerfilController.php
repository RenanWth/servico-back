<?php

namespace App\Http\Controllers;

use App\Services\PerfilService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Perfis', description: 'Gerenciamento de perfis de usuários')]
class PerfilController extends Controller
{
    public function __construct(
        private PerfilService $perfilService
    ) {}

    #[OA\Get(
        path: '/api/perfis',
        summary: 'Lista todos os perfis',
        tags: ['Perfis'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de perfis retornada com sucesso',
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
            $perfis = $this->perfilService->listar();
            return response()->json(['data' => $perfis], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/perfis/{id}',
        summary: 'Busca perfil por ID',
        tags: ['Perfis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do perfil',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Perfil encontrado',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(response: 404, description: 'Perfil não encontrado')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $perfil = $this->perfilService->buscarPorId($id);
            return response()->json(['data' => $perfil], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/perfis',
        summary: 'Cria um novo perfil',
        tags: ['Perfis'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'ADMIN'),
                    new OA\Property(property: 'descricao', type: 'string', example: 'Administrador do sistema')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Perfil criado com sucesso',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:50',
                'descricao' => 'nullable|string'
            ]);

            $perfil = $this->perfilService->criar($validated);
            return response()->json(['data' => $perfil], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/perfis/{id}',
        summary: 'Atualiza um perfil',
        tags: ['Perfis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string'),
                    new OA\Property(property: 'descricao', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Perfil atualizado com sucesso',
                content: new OA\JsonContent(type: 'object')
            ),
            new OA\Response(response: 404, description: 'Perfil não encontrado')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nome' => 'sometimes|string|max:50',
                'descricao' => 'nullable|string'
            ]);

            $perfil = $this->perfilService->atualizar($id, $validated);
            return response()->json(['data' => $perfil], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/perfis/{id}',
        summary: 'Exclui um perfil',
        tags: ['Perfis'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Perfil excluído com sucesso'),
            new OA\Response(response: 404, description: 'Perfil não encontrado'),
            new OA\Response(response: 409, description: 'Não é possível excluir, existem pessoas vinculadas')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->perfilService->excluir($id);
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


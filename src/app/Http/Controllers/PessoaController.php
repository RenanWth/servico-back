<?php

namespace App\Http\Controllers;

use App\Services\PessoaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Pessoas', description: 'Gerenciamento de pessoas')]
class PessoaController extends Controller
{
    public function __construct(
        private PessoaService $pessoaService
    ) {}

    #[OA\Get(
        path: '/api/pessoas',
        summary: 'Lista todas as pessoas',
        tags: ['Pessoas'],
        parameters: [
            new OA\Parameter(name: 'ativo', in: 'query', schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'perfil_id', in: 'query', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de pessoas',
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
            $filtros = $request->only(['ativo', 'perfil_id']);
            $pessoas = $this->pessoaService->listar($filtros);
            return response()->json(['data' => $pessoas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/pessoas/ativas',
        summary: 'Lista apenas pessoas ativas',
        tags: ['Pessoas'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de pessoas ativas',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function ativas(): JsonResponse
    {
        try {
            $pessoas = $this->pessoaService->listarAtivas();
            return response()->json(['data' => $pessoas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/pessoas/{id}',
        summary: 'Busca pessoa por ID',
        tags: ['Pessoas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pessoa encontrada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Pessoa não encontrada')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $pessoa = $this->pessoaService->buscarPorId($id);
            return response()->json(['data' => $pessoa], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/pessoas',
        summary: 'Cria uma nova pessoa',
        tags: ['Pessoas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome_completo', 'perfil_id'],
                properties: [
                    new OA\Property(property: 'nome_completo', type: 'string', example: 'João Silva'),
                    new OA\Property(property: 'cpf', type: 'string', example: '123.456.789-00'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                    new OA\Property(property: 'telefone', type: 'string', example: '(51) 99999-9999'),
                    new OA\Property(property: 'dt_nascimento', type: 'string', format: 'date', example: '1990-01-01'),
                    new OA\Property(property: 'genero', type: 'string', example: 'M'),
                    new OA\Property(property: 'perfil_id', type: 'integer', example: 1),
                    new OA\Property(property: 'ativo', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Pessoa criada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nome_completo' => 'required|string|max:200',
                'cpf' => 'nullable|string|max:14|unique:pessoas,cpf',
                'email' => 'nullable|email|max:150|unique:pessoas,email',
                'telefone' => 'nullable|string|max:20',
                'dt_nascimento' => 'nullable|date|before:today',
                'genero' => 'nullable|string|max:20',
                'perfil_id' => 'required|exists:perfis,id',
                'ativo' => 'sometimes|boolean'
            ]);

            $pessoa = $this->pessoaService->criar($validated);
            return response()->json(['data' => $pessoa], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Put(
        path: '/api/pessoas/{id}',
        summary: 'Atualiza uma pessoa',
        tags: ['Pessoas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome_completo', type: 'string'),
                    new OA\Property(property: 'cpf', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'telefone', type: 'string'),
                    new OA\Property(property: 'dt_nascimento', type: 'string', format: 'date'),
                    new OA\Property(property: 'genero', type: 'string'),
                    new OA\Property(property: 'perfil_id', type: 'integer'),
                    new OA\Property(property: 'ativo', type: 'boolean')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Pessoa atualizada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Pessoa não encontrada')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nome_completo' => 'sometimes|string|max:200',
                'cpf' => 'nullable|string|max:14|unique:pessoas,cpf,' . $id,
                'email' => 'nullable|email|max:150|unique:pessoas,email,' . $id,
                'telefone' => 'nullable|string|max:20',
                'dt_nascimento' => 'nullable|date|before:today',
                'genero' => 'nullable|string|max:20',
                'perfil_id' => 'sometimes|exists:perfis,id',
                'ativo' => 'sometimes|boolean'
            ]);

            $pessoa = $this->pessoaService->atualizar($id, $validated);
            return response()->json(['data' => $pessoa], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/pessoas/{id}/ativar',
        summary: 'Ativa uma pessoa',
        tags: ['Pessoas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pessoa ativada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Pessoa não encontrada')
        ]
    )]
    public function ativar(int $id): JsonResponse
    {
        try {
            $pessoa = $this->pessoaService->ativar($id);
            return response()->json(['data' => $pessoa], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/pessoas/{id}/desativar',
        summary: 'Desativa uma pessoa',
        tags: ['Pessoas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pessoa desativada', content: new OA\JsonContent(type: 'object')),
            new OA\Response(response: 404, description: 'Pessoa não encontrada')
        ]
    )]
    public function desativar(int $id): JsonResponse
    {
        try {
            $pessoa = $this->pessoaService->desativar($id);
            return response()->json(['data' => $pessoa], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/pessoas/{id}',
        summary: 'Exclui uma pessoa',
        tags: ['Pessoas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Pessoa excluída'),
            new OA\Response(response: 404, description: 'Pessoa não encontrada'),
            new OA\Response(response: 409, description: 'Não é possível excluir, existem relacionamentos')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->pessoaService->excluir($id);
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


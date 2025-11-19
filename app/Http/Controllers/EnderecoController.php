<?php

namespace App\Http\Controllers;

use App\Services\EnderecoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Endereços', description: 'Gerenciamento de endereços de pessoas')]
class EnderecoController extends Controller
{
    public function __construct(
        private EnderecoService $enderecoService
    ) {}

    #[OA\Get(
        path: '/api/pessoas/{pessoaId}/enderecos',
        summary: 'Lista endereços de uma pessoa',
        tags: ['Endereços'],
        parameters: [
            new OA\Parameter(name: 'pessoaId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de endereços',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
                    ]
                )
            )
        ]
    )]
    public function index(int $pessoaId): JsonResponse
    {
        try {
            $enderecos = $this->enderecoService->listarPorPessoa($pessoaId);
            return response()->json(['data' => $enderecos], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/enderecos/{id}',
        summary: 'Busca endereço por ID',
        tags: ['Endereços'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Endereço encontrado'),
            new OA\Response(response: 404, description: 'Endereço não encontrado')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $endereco = $this->enderecoService->buscarPorId($id);
            return response()->json(['data' => $endereco], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Post(
        path: '/api/enderecos',
        summary: 'Cria um novo endereço',
        tags: ['Endereços'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['pessoa_id', 'cep', 'logradouro', 'numero', 'bairro'],
                properties: [
                    new OA\Property(property: 'pessoa_id', type: 'integer'),
                    new OA\Property(property: 'cep', type: 'string', example: '95900-000'),
                    new OA\Property(property: 'logradouro', type: 'string', example: 'Rua Principal'),
                    new OA\Property(property: 'numero', type: 'string', example: '123'),
                    new OA\Property(property: 'complemento', type: 'string', example: 'Apto 101'),
                    new OA\Property(property: 'bairro', type: 'string', example: 'Centro'),
                    new OA\Property(property: 'principal', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Endereço criado'),
            new OA\Response(response: 400, description: 'Dados inválidos')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'pessoa_id' => 'required|exists:pessoas,id',
                'cep' => 'required|string|max:10',
                'logradouro' => 'required|string|max:200',
                'numero' => 'required|string|max:20',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'required|string|max:100',
                'principal' => 'sometimes|boolean'
            ]);

            $endereco = $this->enderecoService->criar($validated);
            return response()->json(['data' => $endereco], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    #[OA\Put(
        path: '/api/enderecos/{id}',
        summary: 'Atualiza um endereço',
        tags: ['Endereços'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'cep', type: 'string'),
                    new OA\Property(property: 'logradouro', type: 'string'),
                    new OA\Property(property: 'numero', type: 'string'),
                    new OA\Property(property: 'complemento', type: 'string'),
                    new OA\Property(property: 'bairro', type: 'string'),
                    new OA\Property(property: 'principal', type: 'boolean')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Endereço atualizado'),
            new OA\Response(response: 404, description: 'Endereço não encontrado')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cep' => 'sometimes|string|max:10',
                'logradouro' => 'sometimes|string|max:200',
                'numero' => 'sometimes|string|max:20',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'sometimes|string|max:100',
                'principal' => 'sometimes|boolean'
            ]);

            $endereco = $this->enderecoService->atualizar($id, $validated);
            return response()->json(['data' => $endereco], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Patch(
        path: '/api/enderecos/{id}/principal',
        summary: 'Define endereço como principal',
        tags: ['Endereços'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Endereço definido como principal'),
            new OA\Response(response: 404, description: 'Endereço não encontrado')
        ]
    )]
    public function definirPrincipal(int $id): JsonResponse
    {
        try {
            $endereco = $this->enderecoService->definirComoPrincipal($id);
            return response()->json(['data' => $endereco], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    #[OA\Delete(
        path: '/api/enderecos/{id}',
        summary: 'Exclui um endereço',
        tags: ['Endereços'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Endereço excluído'),
            new OA\Response(response: 404, description: 'Endereço não encontrado')
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->enderecoService->excluir($id);
            return response()->json(null, 204);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

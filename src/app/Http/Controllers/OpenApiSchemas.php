<?php

namespace App\Http\Controllers;

/**
 * Este arquivo contém as definições de schemas OpenAPI
 * Usado como referência para documentação da API
 */
class OpenApiSchemas
{
    /**
     * Schemas serão definidos via anotações OpenAPI nos controllers
     * Exemplo de schema inline:
     * 
     * @OA\Schema(
     *     schema="Perfil",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="nome", type="string", example="ADMIN"),
     *     @OA\Property(property="descricao", type="string", example="Administrador do sistema"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */
}


<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'API Serviço Backend',
    description: 'API RESTful para gerenciamento de serviços comunitários, incluindo pessoas, perfis, missões, notícias e doações.'
)]
#[OA\Server(
    url: '/',
    description: 'API Server'
)]
#[OA\Tag(name: 'Perfis', description: 'Gerenciamento de perfis de usuários')]
#[OA\Tag(name: 'Pessoas', description: 'Gerenciamento de pessoas')]
#[OA\Tag(name: 'Endereços', description: 'Gerenciamento de endereços')]
#[OA\Tag(name: 'Voluntários', description: 'Gerenciamento de voluntários')]
#[OA\Tag(name: 'Missões', description: 'Gerenciamento de missões')]
#[OA\Tag(name: 'Candidaturas', description: 'Gerenciamento de candidaturas a missões')]
#[OA\Tag(name: 'Notícias', description: 'Gerenciamento de notícias')]
#[OA\Tag(name: 'Imagens', description: 'Gerenciamento de imagens de notícias')]
#[OA\Tag(name: 'Pontos de Coleta', description: 'Gerenciamento de pontos de coleta')]
#[OA\Tag(name: 'Necessidades', description: 'Gerenciamento de necessidades dos pontos')]
#[OA\Tag(name: 'Doações', description: 'Gerenciamento de doações')]
#[OA\Tag(name: 'Itens de Doação', description: 'Gerenciamento de itens de doação')]
class ApiDocumentation
{
    // Este arquivo serve apenas para definir informações gerais da API
    // Os schemas e endpoints são definidos nos controllers individuais
}


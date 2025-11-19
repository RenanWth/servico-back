<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerifyAuthToken
{
    public function handle(Request $request, Closure $next)
    {
        //Token do frontend
        // front vai ter que ter esse token em todos os headers pq aqui pego o token no header para enviar para o servico 2 e testar se pode acessar a rota, para 
        // todas as rotas isso acontece.
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não informado'], 401);
        }

        //Chamando API de autenticação
        $response = Http::withToken($token)
            ->get(env('AUTH_SERVICE_URL') . '/api/auth/validate');

        if ($response->failed() || !$response->json('valid')) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        //Adicionando user autenticado no request
        $request->merge([
            'auth_user' => $response->json('user')
        ]);

        return $next($request);
    }
}

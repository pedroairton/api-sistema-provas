<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['message' => 'Token de acesso não fornecido'], 401);
            }

            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken) {
                return response()->json(['message' => 'Token inválido'], 401);
            }

            if ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at)) {
                // apaga token expirado
                $accessToken->delete();
                return response()->json(['message' => 'Token expirado'], 401);
            }

            $admin = $accessToken->tokenable;

            if (!$admin || !$admin instanceof \App\Models\Admin) {
                return response()->json(['message' => 'Acesso não autorizado'], 401);
            }

        } catch (\Exception $e) {
           
            return response()->json(['message' => 'Erro de autenticação'], 500);
        }

        return $next($request);
    }
}

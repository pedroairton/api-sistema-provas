<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UsuarioAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if(!Auth::guard('web')->check()){
        //     return response()->json(['message' => 'Acesso não autorizado'], 401);
        // }
        if(!$request->user() || !$request->user() instanceof \App\Models\Usuario){
            return response()->json(['message' => 'Acesso não autorizado'], 401);
        }

        return $next($request);
    }
}

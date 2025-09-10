<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'nullable|string|max:100',
            'email' => 'required|email|unique:usuarios',
            'senha' => 'required|min:6'
        ]);

        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha_hash' => Hash::make($request->senha)
        ]);

        // Auth::guard('web')->login($usuario);
        $token = $usuario->createToken('usuario-token')->plainTextToken;

        return response()->json(['message' => 'Usuário registrado', 'token' => $token]);
    }
    public function login(Request $request)
    {
        $credenciais = [
            'email' => $request->email,
            'senha_hash' => $request->senha
        ];

        $usuario = Usuario::where('email', $request->email)->first();
        if (!$usuario || !Hash::check($request->senha, $usuario->senha_hash)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        // Auth::guard('web')->login($usuario);
        $token = $usuario->createToken('usuario-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado',
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'email' => $usuario->email,
                'nome' => $usuario->nome
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Auth::guard('web')->logout();

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado']);
    }
}

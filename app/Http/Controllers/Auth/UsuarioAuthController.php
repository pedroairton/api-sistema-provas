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

        Auth::guard('web')->login($usuario);

        return response()->json(['message' => 'Usuário registrado']);
    }
    public function login(Request $request){
        $credenciais = [
            'email' => $request->email,
            'senha_hash' => $request->senha
        ];

        $usuario = Usuario::where('email', $request->email)->first();

        if(!$usuario || !Hash::check($request->senha, $usuario->senha_hash)){
            return response()->json(['message' => 'Credenciais inválidas']);
        }

        Auth::guard('web')->login($usuario);

        return response()->json(['message' => 'Login realizado']);
    }

    public function logout(){
        Auth::guard('web')->logout();
        return response()->json(['message' => 'Logout realizado']);
    }
}

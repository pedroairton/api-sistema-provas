<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'nome' => 'nullable|string|max:100',
            'email' => 'required|email|unique:admins',
            'senha' => 'required|min:6',
        ]);

        $admin = Admin::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha_hash' => Hash::make($request->senha)
        ]);

        // Auth::guard('admin')->login($admin);
        $expiresAt = now()->addHours(6);
        $token = $admin->createToken('admin-token', ['*'], $expiresAt)->plainTextToken;

        return response()->json(['message' => 'Administrador registrado', 'token' => $token]);
    }
    public function login(Request $request){
        $admin = Admin::where('email', $request->email)->first();

        if(!$admin || !Hash::check($request->senha, $admin->senha_hash)){
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        // Auth::guard('admin')->login($admin);
        $expiresAt = now()->addHours(6);
        $token = $admin->createToken('admin-token', ['*'], $expiresAt)->plainTextToken;

        return response()->json(['message' => 'Login de administrador realizado', 'token' => $token,]);
    }

    public function logout(Request $request){
        // Auth::guard('admin')->logout();
        $request->user()->currentAcessToken()->delete();
        return response()->json(['message' => 'Logout de administrador realizado']);
    }
}

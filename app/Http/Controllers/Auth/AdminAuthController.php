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

        Auth::guard('admin')->login($admin);

        return response()->json(['message' => 'Administrador registrado']);
    }
    public function login(Request $request){
        $admin = Admin::where('email', $request->email)->first();

        if(!$admin || !Hash::check($request->senha, $admin->senha_hash)){
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        Auth::guard('admin')->login($admin);

        return response()->json(['message' => 'Login de administrador realizado']);
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return response()->json(['message' => 'Logout de administrador realizado']);
    }
}

<?php

// auth

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UsuarioAuthController;
use App\Http\Controllers\QuestoesController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\UsuarioAuth;
use Illuminate\Support\Facades\Route;

Route::post('/usuario/register', [UsuarioAuthController::class, 'register']);
Route::post('/usuario/login', [UsuarioAuthController::class, 'login']);
Route::post('/usuario/logout', [UsuarioAuthController::class, 'logout']);

Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

Route::post('/questao/criar', [QuestoesController::class, 'criaQuestao']);
Route::get('/questoes', [QuestoesController::class, 'getQuestoes']);
Route::get('/questao/{questao}', [QuestoesController::class, 'getQuestao']);


// Rotas protegidas para usuários
// Ex: responder perguntas


// Rotas protegidas para admins
// Ex: cadastrar perguntas e alternativas


Route::middleware(['auth:sanctum', AdminAuth::class])->prefix('admin')->group(function () {
    Route::get('/home', function () {
        // return dd('Rota protegida para admin');
        return response()->json(['message' => 'Rota protegida para admin'], 200);
    });
});
Route::middleware(['auth:sanctum', UsuarioAuth::class])->prefix('usuario')->group(function () {
    Route::get('/home', function () {
        // return dd('Rota protegida para usuario');
        return response()->json(['message' => 'Rota protegida para usuário'], 200);
    });
    Route::post('/responder', [QuestoesController::class, 'respondeQuestao']);
});

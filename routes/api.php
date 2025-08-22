<?php

// auth

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UsuarioAuthController;
use App\Http\Controllers\QuestoesController;
use Illuminate\Support\Facades\Route;

Route::post('/usuario/register', [UsuarioAuthController::class, 'register']);
Route::post('/usuario/login', [UsuarioAuthController::class, 'login']);
Route::post('/usuario/logout', [UsuarioAuthController::class, 'logout']);

Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

Route::post('/questao/criar', [QuestoesController::class, 'criaQuestao']);

// Rotas protegidas para usuários
// Ex: responder perguntas
Route::middleware(['auth.web'])->group(function () {
    Route::get('/usuario/home', function () {
        return dd('Rota protegida para usuário');
    });
});

// Rotas protegidas para admins
// Ex: cadastrar perguntas e alternativas
Route::middleware(['auth.admin'])->prefix('admin')->group(function () {
    Route::get('/home', function () {
        return dd('Rota protegida para admin');
    });
});

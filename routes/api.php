<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JogoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aqui você registra todas as rotas da sua API.
| Elas ficam acessíveis via prefixo /api/ por padrão.
*/

// Rotas RESTful para o recurso Jogo
Route::get('/jogos', [JogoController::class, 'index']);          // Lista todos os jogos
Route::get('/jogos/{id}', [JogoController::class, 'show']);      // Mostra um jogo específico
Route::post('/jogos', [JogoController::class, 'store']);         // Cria um jogo manualmente
Route::put('/jogos/{id}', [JogoController::class, 'update']);    // Atualiza um jogo
Route::delete('/jogos/{id}', [JogoController::class, 'destroy']); // Apaga um jogo

// Rotas personalizadas para lógica do jogo
Route::post('/iniciar-jogo', [JogoController::class, 'iniciarJogo']);  
Route::post('/validar-tentativa/{idJogo}', [JogoController::class, 'validarTentativa']);

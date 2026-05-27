<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JogoController;

// Rota para iniciar um novo jogo
Route::post('/iniciar-jogo', [JogoController::class, 'iniciarJogo']);

// Rota para validar uma tentativa (precisa receber o id do jogo)
Route::post('/validar-tentativa/{idJogo}', [JogoController::class, 'validarTentativa']);

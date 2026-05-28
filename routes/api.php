<?php

use App\Http\Controllers\JogoController;
use Illuminate\Support\Facades\Route;

Route::post('/iniciar-jogo', [JogoController::class, 'iniciarJogo']);
Route::post('/validar-tentativa', [JogoController::class, 'validarTentativa']);

Route::post('/jogos', [JogoController::class, 'iniciarJogo']);
Route::post('/jogos/{idJogo}/tentativas', [JogoController::class, 'validarTentativaPorJogo']);
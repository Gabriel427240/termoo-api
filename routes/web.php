<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JogoController;

Route::post('/jogos', [JogoController::class, 'iniciarJogo']);

Route::post(
    '/jogos/{idJogo}/tentativas',
    [JogoController::class, 'validarTentativa']
);
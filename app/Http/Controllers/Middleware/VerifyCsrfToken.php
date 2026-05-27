<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * As rotas abaixo estão isentas da verificação CSRF.
     */
    protected $except = [
        'api/iniciar-jogo',
        'api/validar-tentativa',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jogo extends Model
{
    protected $table = 'jogos';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'palavra_secreta',
        'tentativas_restantes',
        'venceu',
    ];

    protected $casts = [
        'venceu' => 'boolean',
    ];
}
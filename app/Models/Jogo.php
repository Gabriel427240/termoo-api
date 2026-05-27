<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jogo extends Model
{
    protected $table = 'jogos';
    protected $fillable = ['id', 'palavra_secreta', 'tentativas_restantes'];
    public $incrementing = false; // usa UUID
    protected $keyType = 'string';
}

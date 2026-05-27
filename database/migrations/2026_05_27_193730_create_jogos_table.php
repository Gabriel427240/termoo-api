<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('jogos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('palavra_secreta');
            $table->integer('tentativas_restantes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jogos');
    }
};

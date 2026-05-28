<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jogos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('palavra_secreta', 5);
            $table->integer('tentativas_restantes')->default(6);
            $table->boolean('venceu')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jogos');
    }
};
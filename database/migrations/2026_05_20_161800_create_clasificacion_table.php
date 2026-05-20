<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clasificacion', function (Blueprint $table) {
            $table->id();
            $table->string('temporada');
            $table->string('competicion')->default('Primera Federación');
            $table->foreignId('equipo_id')->constrained('equipos')->cascadeOnDelete();
            $table->integer('posicion');
            $table->integer('partidos_jugados')->default(0);
            $table->integer('victorias')->default(0);
            $table->integer('empates')->default(0);
            $table->integer('derrotas')->default(0);
            $table->integer('goles_favor')->default(0);
            $table->integer('goles_contra')->default(0);
            $table->integer('puntos')->default(0);
            $table->dateTime('actualizado_en')->nullable();
            $table->timestamps();

            $table->unique(['temporada', 'competicion', 'equipo_id']);
            $table->index(['temporada', 'competicion', 'posicion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clasificacion');
    }
};

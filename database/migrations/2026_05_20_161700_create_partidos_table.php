<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->string('temporada');
            $table->integer('jornada')->nullable();
            $table->string('competicion')->default('Primera Federación');
            $table->foreignId('local_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->foreignId('visitante_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->string('local_nombre');
            $table->string('visitante_nombre');
            $table->string('local_escudo')->nullable();
            $table->string('visitante_escudo')->nullable();
            $table->integer('goles_local')->nullable();
            $table->integer('goles_visitante')->nullable();
            $table->dateTime('fecha');
            $table->string('estadio')->nullable();
            $table->string('arbitro')->nullable();
            $table->enum('estado', [
                'programado',
                'en_juego',
                'finalizado',
                'aplazado',
                'cancelado',
            ])->default('programado');
            $table->string('compralaentrada_evento_id')->nullable();
            $table->string('compralaentrada_sesion_id')->nullable();
            $table->string('resumen_url')->nullable();
            $table->timestamps();

            $table->index('temporada');
            $table->index('fecha');
            $table->index('estado');
            $table->index(['temporada', 'jornada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};

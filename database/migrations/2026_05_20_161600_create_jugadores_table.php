<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jugadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->integer('dorsal')->nullable();
            $table->enum('posicion', ['portero', 'defensa', 'centrocampista', 'delantero']);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('foto')->nullable();
            $table->text('biografia')->nullable();
            $table->integer('altura_cm')->nullable();
            $table->integer('peso_kg')->nullable();
            $table->string('equipo')->default('primer-equipo');
            $table->string('temporada')->default('2025-2026');
            $table->unsignedBigInteger('sofascore_id')->nullable();
            $table->integer('goles')->default(0);
            $table->integer('asistencias')->default(0);
            $table->integer('minutos_jugados')->default(0);
            $table->integer('partidos_jugados')->default(0);
            $table->integer('tarjetas_amarillas')->default(0);
            $table->integer('tarjetas_rojas')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('temporada');
            $table->index('equipo');
            $table->index('activo');
            $table->index('posicion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jugadores');
    }
};

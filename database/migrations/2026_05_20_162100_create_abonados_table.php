<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abonados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('numero_socio')->unique();
            $table->string('temporada');
            $table->string('tipo_abono')->default('general');
            $table->string('grada')->nullable();
            $table->string('sector')->nullable();
            $table->string('fila')->nullable();
            $table->string('asiento')->nullable();
            $table->date('valido_desde');
            $table->date('valido_hasta');
            $table->boolean('activo')->default(true);
            $table->decimal('precio_pagado', 10, 2)->nullable();
            $table->timestamps();

            $table->index('temporada');
            $table->index('activo');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonados');
    }
};

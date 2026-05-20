<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('descripcion')->nullable();
            $table->enum('tipo', ['porcentaje', 'cantidad']);
            $table->decimal('valor', 10, 2);
            $table->decimal('compra_minima', 10, 2)->nullable();
            $table->integer('usos_max')->nullable();
            $table->integer('usos_actual')->default(0);
            $table->dateTime('valido_desde');
            $table->dateTime('valido_hasta');
            $table->boolean('solo_socios')->default(false);
            $table->json('canales')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
            $table->index(['valido_desde', 'valido_hasta']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};

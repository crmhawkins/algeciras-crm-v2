<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->boolean('disponible_web')->default(true);
            $table->boolean('disponible_app')->default(true);
            $table->boolean('disponible_tpv')->default(true);
            $table->decimal('comision_pct', 5, 2)->default(0);
            $table->json('configuracion')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
};

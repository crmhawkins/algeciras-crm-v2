<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofertas_socios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocio_id')->constrained('negocios_locales')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->decimal('descuento_pct', 5, 2)->nullable();
            $table->text('condiciones')->nullable();
            $table->date('valido_desde');
            $table->date('valido_hasta');
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamps();

            $table->index('negocio_id');
            $table->index('activo');
            $table->index(['valido_desde', 'valido_hasta']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas_socios');
    }
};

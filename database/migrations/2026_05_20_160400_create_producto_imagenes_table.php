<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('variante_id')->nullable()->constrained('producto_variantes')->nullOnDelete();
            $table->string('ruta');
            $table->string('alt')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('es_principal')->default(false);
            $table->timestamps();

            $table->index('producto_id');
            $table->index('variante_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_imagenes');
    }
};

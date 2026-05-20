<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_variantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('sku')->unique();
            $table->json('atributos')->nullable();
            $table->decimal('precio_extra', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('imagen')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_variantes');
    }
};

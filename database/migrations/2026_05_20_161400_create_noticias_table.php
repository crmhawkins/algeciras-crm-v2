<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('titulo');
            $table->text('extracto')->nullable();
            $table->longText('contenido')->nullable();
            $table->foreignId('categoria_id')->nullable()->constrained('noticias_categorias')->nullOnDelete();
            $table->foreignId('autor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('imagen_destacada')->nullable();
            $table->string('meta_titulo')->nullable();
            $table->text('meta_descripcion')->nullable();
            $table->json('etiquetas')->nullable();
            $table->boolean('publicada')->default(false);
            $table->dateTime('publicada_en')->nullable();
            $table->unsignedBigInteger('vistas')->default(0);
            $table->boolean('destacada_home')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('publicada');
            $table->index('publicada_en');
            $table->index('categoria_id');
            $table->index('destacada_home');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};

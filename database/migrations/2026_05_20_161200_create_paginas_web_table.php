<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paginas_web', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('titulo');
            $table->longText('contenido')->nullable();
            $table->string('meta_titulo')->nullable();
            $table->text('meta_descripcion')->nullable();
            $table->string('imagen_destacada')->nullable();
            $table->boolean('publicada')->default(false);
            $table->integer('orden_menu')->nullable();
            $table->foreignId('padre_id')->nullable()->constrained('paginas_web')->nullOnDelete();
            $table->timestamps();

            $table->index('publicada');
            $table->index('padre_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paginas_web');
    }
};

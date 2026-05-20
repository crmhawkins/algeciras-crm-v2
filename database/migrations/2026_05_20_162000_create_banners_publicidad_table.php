<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners_publicidad', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('imagen_desktop');
            $table->string('imagen_mobile')->nullable();
            $table->string('enlace')->nullable();
            $table->enum('posicion', [
                'home_hero',
                'home_lateral',
                'tienda_top',
                'noticias_lateral',
                'app_inicio',
            ]);
            $table->dateTime('desde')->nullable();
            $table->dateTime('hasta')->nullable();
            $table->integer('orden')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('impresiones')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('posicion');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners_publicidad');
    }
};

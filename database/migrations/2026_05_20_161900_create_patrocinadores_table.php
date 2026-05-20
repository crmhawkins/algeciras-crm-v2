<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrocinadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('logo');
            $table->string('enlace_web')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['principal', 'oficial', 'colaborador', 'proveedor']);
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('tipo');
            $table->index('activo');
            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrocinadores');
    }
};

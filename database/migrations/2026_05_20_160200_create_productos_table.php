<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('descripcion_corta')->nullable();
            $table->text('descripcion_larga')->nullable();
            $table->string('sku')->unique();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('marca')->nullable();
            $table->decimal('precio_base', 10, 2);
            $table->decimal('precio_oferta', 10, 2)->nullable();
            $table->decimal('coste', 10, 2)->nullable();
            $table->decimal('iva_pct', 5, 2)->default(21);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('peso_gramos')->nullable();
            $table->boolean('es_destacado')->default(false);
            $table->boolean('es_novedad')->default(false);
            $table->boolean('visible_web')->default(true);
            $table->boolean('visible_app')->default(true);
            $table->boolean('visible_tpv')->default(true);
            $table->boolean('requiere_envio')->default(true);
            $table->string('meta_titulo')->nullable();
            $table->text('meta_descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('categoria_id');
            $table->index('visible_web');
            $table->index('visible_app');
            $table->index('visible_tpv');
            $table->index('es_destacado');
            $table->index('es_novedad');
            $table->index('stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

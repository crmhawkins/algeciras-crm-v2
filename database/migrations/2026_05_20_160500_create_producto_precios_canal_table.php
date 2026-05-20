<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_precios_canal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('variante_id')->nullable()->constrained('producto_variantes')->nullOnDelete();
            $table->enum('canal', ['web', 'app', 'tpv']);
            $table->decimal('precio', 10, 2);
            $table->decimal('descuento_pct', 5, 2)->nullable();
            $table->decimal('descuento_socio_pct', 5, 2)->nullable();
            $table->dateTime('desde')->nullable();
            $table->dateTime('hasta')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['producto_id', 'canal']);
            $table->index(['variante_id', 'canal']);
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_precios_canal');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('variante_id')->nullable()->constrained('producto_variantes')->nullOnDelete();
            $table->enum('tipo', [
                'entrada',
                'salida',
                'ajuste',
                'venta_web',
                'venta_app',
                'venta_tpv',
                'devolucion',
            ]);
            $table->integer('cantidad');
            $table->integer('stock_antes');
            $table->integer('stock_despues');
            $table->text('motivo')->nullable();
            $table->string('referencia_tipo')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('producto_id');
            $table->index('variante_id');
            $table->index('tipo');
            $table->index(['referencia_tipo', 'referencia_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movimientos');
    }
};

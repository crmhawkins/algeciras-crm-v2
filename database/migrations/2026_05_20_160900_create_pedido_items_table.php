<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->foreignId('variante_id')->nullable()->constrained('producto_variantes')->nullOnDelete();
            $table->string('sku');
            $table->string('nombre_producto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento_pct', 5, 2)->default(0);
            $table->decimal('iva_pct', 5, 2)->default(21);
            $table->decimal('subtotal_linea', 10, 2);
            $table->decimal('total_linea', 10, 2);
            $table->timestamps();

            $table->index('pedido_id');
            $table->index('producto_id');
            $table->index('variante_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};

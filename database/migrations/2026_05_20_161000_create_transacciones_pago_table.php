<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->string('gateway');
            $table->string('referencia');
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 3)->default('EUR');
            $table->enum('estado', [
                'iniciada',
                'autorizada',
                'capturada',
                'rechazada',
                'reembolsada',
            ])->default('iniciada');
            $table->json('respuesta_gateway')->nullable();
            $table->text('error_mensaje')->nullable();
            $table->timestamps();

            $table->index('pedido_id');
            $table->index('gateway');
            $table->index('referencia');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones_pago');
    }
};

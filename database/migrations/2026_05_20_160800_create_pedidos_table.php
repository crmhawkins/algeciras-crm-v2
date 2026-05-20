<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->enum('canal', ['web', 'app', 'tpv']);
            $table->enum('estado', [
                'borrador',
                'pendiente_pago',
                'pagado',
                'preparando',
                'enviado',
                'entregado',
                'cancelado',
                'devuelto',
            ])->default('borrador');
            $table->foreignId('cliente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->string('telefono_cliente')->nullable();
            $table->text('direccion_envio')->nullable();
            $table->string('cp_envio', 10)->nullable();
            $table->string('ciudad_envio')->nullable();
            $table->string('provincia_envio')->nullable();
            $table->string('pais_envio', 2)->default('ES');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('iva', 10, 2);
            $table->decimal('gastos_envio', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('metodo_pago', [
                'redsys',
                'bizum',
                'efectivo',
                'transferencia',
                'datafono_tpv',
            ])->nullable();
            $table->string('referencia_pago')->nullable();
            $table->dateTime('pagado_en')->nullable();
            $table->dateTime('cancelado_en')->nullable();
            $table->text('notas_internas')->nullable();
            $table->text('notas_cliente')->nullable();
            $table->foreignId('cajero_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('canal');
            $table->index('estado');
            $table->index('cliente_id');
            $table->index('cajero_id');
            $table->index('pagado_en');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};

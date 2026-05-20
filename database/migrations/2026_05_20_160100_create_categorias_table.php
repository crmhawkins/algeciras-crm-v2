<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('imagen')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('visible_web')->default(true);
            $table->boolean('visible_app')->default(true);
            $table->boolean('visible_tpv')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'orden']);
            $table->index('visible_web');
            $table->index('visible_app');
            $table->index('visible_tpv');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};

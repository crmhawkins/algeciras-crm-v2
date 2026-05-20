<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('escudo')->nullable();
            $table->string('ciudad')->nullable();
            $table->unsignedBigInteger('sofascore_id')->nullable();
            $table->timestamps();

            $table->index('sofascore_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};

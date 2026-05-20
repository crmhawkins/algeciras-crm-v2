<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telefono')->nullable()->after('email');
            $table->string('dni', 20)->nullable()->after('telefono');
            $table->string('avatar')->nullable()->after('dni');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telefono', 'dni', 'avatar']);
        });
    }
};

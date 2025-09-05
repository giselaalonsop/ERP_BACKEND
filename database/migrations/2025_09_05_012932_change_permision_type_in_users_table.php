<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cambia 'permision' por 'permissions' si ese es tu nombre real de columna
        Schema::table('users', function (Blueprint $table) {
            $table->longText('permissions')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ⚠️ Solo funcionará si todos los valores son JSON válido
            $table->json('permissions')->nullable()->change();
        });
    }
};

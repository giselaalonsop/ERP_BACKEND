<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->json('pago_movil')->nullable()->after('logo'); // Campo para configuraciones de pago móvil
            $table->json('transferencias')->nullable()->after('pago_movil'); // Campo para configuraciones de transferencias
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->dropColumn('pago_movil');
            $table->dropColumn('transferencias');
        });
    }
};

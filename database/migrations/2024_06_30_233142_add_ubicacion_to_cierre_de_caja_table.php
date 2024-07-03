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
        Schema::table('cierre_de_cajas', function (Blueprint $table) {
            $table->string('ubicacion')->after('estado'); // Agrega el campo ubicacion sin valor por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cierre_de_cajas', function (Blueprint $table) {
            $table->dropColumn('ubicacion');
        });
    }
};

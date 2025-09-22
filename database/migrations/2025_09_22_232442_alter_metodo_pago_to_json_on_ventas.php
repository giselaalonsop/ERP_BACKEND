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
        Schema::table('ventas', function (Blueprint $table) {
            $table->json('metodo_pago')->nullable()->change(); // o ->longText()->nullable()->change();
        });
    }
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('metodo_pago', 255)->nullable()->change();
        });
    }
};

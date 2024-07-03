<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('configuracions', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('direcciones'); // Campo para logo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('configuracions', function (Blueprint $table) {
            $table->dropColumn('logo'); // Eliminar campo logo
        });
    }
};

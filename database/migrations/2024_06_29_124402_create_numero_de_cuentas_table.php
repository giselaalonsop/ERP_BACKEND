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
        Schema::create('numero_de_cuentas', function (Blueprint $table) {
            $table->id();
            $table->integer('proveedor_id'); // ID del proveedor
            $table->string('banco'); // Nombre del banco
            $table->string('numero_cuenta'); // Número de cuenta
            $table->string('rif_cedula'); // RIF o cédula del titular
            $table->string('telefono'); // Número de teléfono
            $table->boolean('pago_movil')->default(false); // Indica si es pago móvil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('numero_de_cuentas');
    }
};

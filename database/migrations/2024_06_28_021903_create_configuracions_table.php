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
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->decimal('IVA', 5, 2)->default(0); // Campo para IVA
            $table->decimal('porcentaje_ganancia', 5, 2)->default(0); // Campo para porcentaje de ganancia
            $table->string('nombre_empresa')->nullable(); // Campo para nombre de la empresa
            $table->string('telefono')->nullable(); // Campo para teléfono
            $table->string('rif')->nullable(); // Campo para RIF
            $table->string('correo')->nullable(); // Campo para correo
            $table->integer('numero_sucursales')->default(1); // Campo para número de sucursales
            $table->json('direcciones')->nullable(); // Campo para direcciones (almacenado como JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('configuraciones');
    }
};

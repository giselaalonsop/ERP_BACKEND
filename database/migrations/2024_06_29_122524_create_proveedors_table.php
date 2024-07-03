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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre del proveedor
            $table->string('empresa')->nullable(); // Nombre de la empresa del proveedor
            $table->string('telefono')->nullable(); // Teléfono del proveedor
            $table->string('correo')->nullable(); // Correo del proveedor
            $table->string('direccion')->nullable(); // Dirección del proveedor
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
};

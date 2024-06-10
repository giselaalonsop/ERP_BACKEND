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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo_electronico');
            $table->string('numero_de_telefono');
            $table->string('direccion');
            $table->string('cedula')->unique();
            $table->integer('edad');
            $table->integer('numero_de_compras')->default(0);
            $table->integer('cantidad_de_articulos_comprados')->default(0);
            $table->string('estatus')->default('Activo');
            $table->integer('frecuencia')->default(0);
            $table->timestamp('fecha_de_registro')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

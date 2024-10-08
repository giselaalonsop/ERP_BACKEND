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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proveedor_id'); // ID del proveedor
            $table->unsignedBigInteger('usuario_id'); // ID del usuario que realiza la compra
            $table->date('fecha'); // Fecha de la compra
            $table->decimal('monto_total', 10, 2); // Monto total de la compra
            $table->decimal('monto_abonado', 10, 2)->default(0); // Monto abonado
            $table->decimal('monto_restante', 10, 2); // Monto restante
            $table->string('estado')->default('pendiente'); // Estado de la compra (pendiente, pagada, etc.)
            $table->timestamps();

            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('cascade'); // Relación con la tabla proveedores
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade'); // Relación con la tabla users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
};

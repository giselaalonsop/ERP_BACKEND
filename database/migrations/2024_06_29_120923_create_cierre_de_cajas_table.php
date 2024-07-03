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
        Schema::create('cierre_de_cajas', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto_total', 10, 2)->default(0); // Monto total
            $table->decimal('dol_efectivo', 10, 2)->default(0); // Monto en dólares efectivo
            $table->decimal('zelle', 10, 2)->default(0); // Monto en Zelle
            $table->decimal('bs_efectivo', 10, 2)->default(0); // Monto en bolívares efectivo
            $table->decimal('bs_punto_de_venta', 10, 2)->default(0); // Monto en punto de venta
            $table->decimal('bs_pago_movil', 10, 2)->default(0); // Monto en pago móvil
            $table->date('fecha'); // Fecha del cierre de caja
            $table->unsignedBigInteger('usuario_id'); // ID del usuario que realiza el cierre
            $table->string('estado')->default('abierto'); // Estado del cierre de caja
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade'); // Relación con la tabla users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('cierre_de_cajas');
    }
};

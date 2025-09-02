<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('cliente');
            $table->string('usuario');
            $table->dateTime('fecha');
            $table->integer('numero_de_venta');
            $table->decimal('total_venta', 8, 2);
            $table->string('comprobante');
            $table->string('estado');
            $table->string('mayor_o_detal');


            $table->enum('metodo_pago', [
                'dol_efectivo',
                'bs_punto_de_venta',
                'bs_pago_movil',
                'zelle',
                'bs_efectivo',
                'pagar_luego'
            ])->default('bs_punto_de_venta');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};

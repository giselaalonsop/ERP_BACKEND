<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMetodoPagoEnumInVentasTable extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('metodo_pago', [
                'dol_efectivo',
                'bs_punto_de_venta',
                'bs_pago_movil',
                'zelle',
                'bs_efectivo',
                'pagar_luego' // Nueva opción
            ])->change();
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('metodo_pago', [
                'dol_efectivo',
                'bs_punto_de_venta',
                'bs_pago_movil',
                'zelle',
                'bs_efectivo'
            ])->change();
        });
    }
}

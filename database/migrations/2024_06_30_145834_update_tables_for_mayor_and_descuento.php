<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTablesForMayorAndDescuento extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('porcentaje_ganancia_mayor', 8, 2)->default(0);
            $table->string('forma_de_venta_mayor')->nullable();
            $table->integer('cantidad_por_caja')->default(1);
            $table->integer('cantidad_en_stock_mayor')->default(0);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->decimal('descuento', 5, 2)->default(0);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('descuento', 8, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'porcentaje_ganancia_mayor',
                'forma_de_venta_mayor',
                'cantidad_por_caja',
                'cantidad_en_stock_mayor'
            ]);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });
    }
}

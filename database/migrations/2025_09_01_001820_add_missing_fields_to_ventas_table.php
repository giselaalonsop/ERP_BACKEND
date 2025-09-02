<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Ubicación por defecto


            // Totales en Bs y $ (opcionales)
            $table->decimal('total_venta_bs', 8, 2)
                ->nullable()
                ->after('total_venta');


            // Descuento con 0 por defecto
            $table->decimal('descuento', 8, 2)
                ->default(0)
                ->after('total_venta_bs');

            // Habilitar (1=activo)
            $table->boolean('habilitar')
                ->default(1)
                ->after('metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['total_venta_bs', 'descuento', 'habilitar']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Asegura que la tabla existe
        if (!Schema::hasTable('ventas')) return;

        Schema::table('ventas', function (Blueprint $table) {
            // Agrega total_venta_bs si no existe
            if (!Schema::hasColumn('ventas', 'total_venta_bs')) {
                $table->decimal('total_venta_bs', 8, 2)
                    ->nullable()
                    ->after('total_venta'); // quita el ->after(...) si 'total_venta' no existe
            }

            // Agrega habilitar si no existe
            if (!Schema::hasColumn('ventas', 'habilitar')) {
                $table->boolean('habilitar')
                    ->default(1)
                    ->after('metodo_pago'); // quita el ->after(...) si 'metodo_pago' no existe
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ventas')) return;

        Schema::table('ventas', function (Blueprint $table) {
            // Borra solo si existen
            if (Schema::hasColumn('ventas', 'total_venta_bs')) {
                $table->dropColumn('total_venta_bs');
            }
            if (Schema::hasColumn('ventas', 'habilitar')) {
                $table->dropColumn('habilitar');
            }
        });
    }
};

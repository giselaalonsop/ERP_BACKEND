<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Evitar bloqueos por FKs durante el drop
        Schema::disableForeignKeyConstraints();

        // Si existe de intentos anteriores, bórrala
        Schema::dropIfExists('compras');

        Schema::enableForeignKeyConstraints();

        // Recrear con el esquema correcto
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->integer('proveedor_id'); // ID del proveedor
            $table->unsignedBigInteger('usuario_id'); // ID del usuario que realiza la compra
            $table->date('fecha'); // Fecha de la compra
            $table->decimal('monto_total', 10, 2); // Monto total de la compra
            $table->decimal('monto_abonado', 10, 2)->default(0); // Monto abonado
            $table->decimal('monto_restante', 10, 2); // Monto restante
            $table->string('estado')->default('pendiente'); // Estado de la compra (pendiente, pagada, etc.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('compras');
        Schema::enableForeignKeyConstraints();
    }
};

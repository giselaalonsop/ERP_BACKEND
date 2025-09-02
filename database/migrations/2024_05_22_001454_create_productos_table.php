<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria');
            $table->integer('cantidad_en_stock');
            $table->string('unidad_de_medida');
            $table->string('ubicacion')->default('Principal');
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('porcentaje_ganancia', 5, 2);
            $table->string('forma_de_venta');
            $table->date('fecha_entrada');
            $table->date('fecha_caducidad');
            $table->decimal('peso', 10, 2)->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('habilitar')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}

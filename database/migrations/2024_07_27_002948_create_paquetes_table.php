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
        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200)->comment('nombre del paquete');
            $table->decimal('precio',10,2)->nullable()->comment('monto de costo del paquete');
            $table->integer('cantidad_dias')->nullable()->comment('cantidad de dias de duracion del paquete');
            $table->text('descripcion')->nullable()->comment('descripcion del paquete');
            $table->timestamp('fecha_creacion')->nullable()->comment('fecha y hora que se creo el paquete');
            $table->integer('usuario_id')->comment('id del usuario que creo el paquete');
            $table->integer('tiene_fecha_limite')->comment('1 tiene fecha limite de compra, 0 no tiene fecha limite de compra');
            $table->date('fecha_limite_compra')->nullable()->comment('fecha limite hasta donde se puede comprar el paquete');
            $table->string('tipo', 50)->comment('para que tipo de usuario sera, Abogado lider o abogado independiente');
            $table->string('estado', 20)->comment('estado ACTIVO,INACTIVO');
            $table->integer('es_eliminado')->comment('1 es eliminado, 0 no es eliminado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paquetes');
    }
};

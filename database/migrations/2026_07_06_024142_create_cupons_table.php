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
        Schema::create('cupons', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->nullable()->comment('codigo que se genera automaticamente');
            $table->timestamp('fecha_uso')->nullable()->comment('fecha y hora que se uso el codigo en un paquete');
            $table->integer('paquete_id')->comment('id del paquete asignado al cupon');
            $table->integer('usuario_id')->comment('id de la tabla user, quien activo este cupon');
            $table->string('estado', 20)->comment('estado ACTIVO,USADO,INACTIVO');
            $table->integer('es_eliminado')->comment('1 es eliminado, 0 no es eliminado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupons');
    }
};

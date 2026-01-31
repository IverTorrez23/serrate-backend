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
        Schema::create('procurador_pagos', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto', 10, 2)->comment('monto de la transaccion');
            $table->string('tipo', 30)->comment('tipo de transaccin, DEBITO, CREDITO');
            $table->timestamp('fecha_pago')->nullable()->comment('fecha de la transaccion');
            $table->timestamp('fecha_inicio_consulta')->nullable()->comment('fecha de la transaccion');
            $table->timestamp('fecha_fin_consulta')->nullable()->comment('fecha de la transaccion');
            $table->string('glosa', 200)->comment('glosa de la transaccion, escrito por el sistema');
            $table->integer('procurador_id')->comment('id de la tabla users, usuario procurador');
            $table->integer('usuario_id')->comment('id de la tabla users, usuario quien hizo la transaccion');
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
        Schema::dropIfExists('procurador_pagos');
    }
};

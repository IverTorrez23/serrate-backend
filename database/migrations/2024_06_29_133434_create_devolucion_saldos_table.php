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
        Schema::create('devolucion_saldos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_devolucion')->nullable();
            $table->string('glosa', 200)->nullable();
            $table->decimal('monto', 10, 2)->nullable();
            $table->integer('billetera_id')->comment('id de la tabla billeteras');
            $table->integer('usuario_id')->comment('id del usuario que hizo la devolucion');
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
        Schema::dropIfExists('devolucion_saldos');
    }
};

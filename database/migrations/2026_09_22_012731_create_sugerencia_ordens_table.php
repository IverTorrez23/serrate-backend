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
        Schema::create('sugerencia_ordens', function (Blueprint $table) {
            $table->id();
            $table->integer('orden_id')->nullable()->comment('id de la orden que tiene sugerencia registrada');
            $table->integer('sugerencia_gasto_id')->nullable()->comment('id de la sugerencia');
            $table->integer('usuario_id')->comment('id de la tabla user, quien creo el registro');
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
        Schema::dropIfExists('sugerencia_ordens');
    }
};

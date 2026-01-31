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
        Schema::create('parametro_vigencias', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_ultima_vigencia')->nullable()->comment('fecha y hora de ultima vigencia activa de los paquetes de un usuario');
            $table->integer('usuario_id')->comment('id del usuario abogado');
            $table->integer('esta_vigente')->comment('1 = vigente, 0 = no esta vigente');
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
        Schema::dropIfExists('parametro_vigencias');
    }
};

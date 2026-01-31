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
        Schema::create('registro_llamadas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_telefono', 50)->comment('numero de telefono al que se llamo');
            $table->timestamp('fecha_llamada')->nullable()->comment('fecha de la llamada');
            $table->integer('gestion_id')->comment('id de la tabla gestion_alternativas');
            $table->integer('usuario_id')->comment('id de la tabla users, usuario quien hizo la llamada');
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
        Schema::dropIfExists('registro_llamadas');
    }
};

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
        Schema::create('confirmacions', function (Blueprint $table) {
            $table->id();
            $table->integer('confir_sistema')->comment('campo que llena el sistema, 0 indica que la descarga se hizo fuera del plazo, 1 la descarga se hizo dentr del plazo');
            $table->integer('confir_abogado')->comment('0 rechazo del abogado, 1 aprobado por el abogado, siempre y cuando la fecha del abogado no este vacia');
            $table->timestamp('fecha_confir_abogado')->nullable()->comment('fecha que se pronuncio el abogado');
            $table->integer('confir_contador')->comment('0 el contador aun no hizo la recepcion de saldos, 1 el contador ya hizo recepcion de saldos de las descargas');
            $table->timestamp('fecha_confir_contador')->nullable()->comment('fecha que el contador hizo la recepcion de saldo');
            $table->text('justificacion_rechazo')->nullable()->comment('detalle del porque el abogado rechazo la descarga');
            $table->integer('descarga_id')->comment('id de la tabla procuraduria_descargas');
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
        Schema::dropIfExists('confirmacions');
    }
};

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
        Schema::create('gestion_alternativas', function (Blueprint $table) {
            $table->id();
            $table->text('solicitud_gestion')->nullable()->comment('redaccion del procurador solicitando gestion alternativa');
            $table->timestamp('fecha_solicitud')->nullable()->comment('fecha y hora de la solicitud del procurador');
            $table->integer('tribunal_id')->nullable()->comment('id de la tabla tribunals');
            $table->integer('cuerpo_expediente_id')->nullable()->comment('id de la tabla cuerpo_expedientes relacionado al triunal');
            $table->text('detalle_gestion')->nullable()->comment('respuesta del abogado');
            $table->timestamp('fecha_respuesta')->nullable()->comment('fecha y hora de la respuesta del abogado');
            $table->integer('orden_id')->nullable()->comment('id de la tabla ordens');
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
        Schema::dropIfExists('gestion_alternativas');
    }
};

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
        Schema::create('ordens', function (Blueprint $table) {
            $table->id();
            $table->text('entrega_informacion')->nullable()->comment('informacion redactada por el abogado o administrador');
            $table->text('entrega_documentacion')->nullable()->comment('redaccion sobre la documentacion que entrega el abogado  o admin');
            $table->timestamp('fecha_inicio')->nullable()->comment('fecha y hora de inicio y vigencia de la orden');
            $table->timestamp('fecha_fin')->nullable()->comment('fecha y hora de finalizacion de vigencia de la orden');
            $table->timestamp('fecha_giro')->nullable()->comment('fecha y hora que se giro la orden (lo pone el sistema)');
            $table->string('plazo_hora', 30)->nullable()->comment('plazo en horas para realizar la orden');
            $table->timestamp('fecha_recepcion')->nullable()->comment('fecha y hora que acepta la orden el procurador');
            $table->string('etapa_orden', 30)->nullable()->comment('etapa de la orden (GIRADA,ACEPTADA,PRESUPUESTADA,DINERO_ENTREGADO,DESCARGADA,PRONUNCIO_ABOGADO,PRONUNCIO_CONTADOR,CERRADA)');
            $table->string('calificacion', 20)->nullable()->comment('Calificacion de la orden, SUFICIENTE, INSUFICIENTE');
            $table->integer('prioridad')->comment('numero de prioridad de la orden');
            $table->timestamp('fecha_cierre')->nullable()->comment('fecha y hora del cierre de la orden');
            $table->string('girada_por', 20)->comment('Indica el tipo de usuario que giro la orden, (ADMINISTRADOR,ABOGADO)');
            $table->timestamp('fecha_ini_bandera')->nullable()->comment('fecha y hora bandera de la orden para sber desde que fecha y hora se puede enviar notificacion');
            $table->integer('notificado')->comment('indica si se envio notificacion  a los usuarios, 1 notificado, 0 no esta notificaco');
            $table->string('lugar_ejecucion', 100)->comment('lugar de ejecucion sugerido');
            $table->text('sugerencia_presupuesto')->nullable()->comment('presupuesto sugerido por el procurador');
            $table->integer('tiene_propina')->comment('1 tiene propina, 0 no tiene propina');
            $table->decimal('propina',10,2)->nullable()->comment('monto de la propina, si hay');
            $table->integer('causa_id')->comment('id de la tabla causa');
            $table->integer('procurador_id')->comment('id del usuario procurador, o procurador maestro');
            $table->integer('matriz_id')->comment('id de la tabla matriz_cotizacions');
            $table->integer('usuario_id')->comment('id de la tabla user, quien giro la orden');

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
        Schema::dropIfExists('ordens');
    }
};

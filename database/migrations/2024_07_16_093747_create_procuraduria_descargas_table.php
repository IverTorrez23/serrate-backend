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
        Schema::create('procuraduria_descargas', function (Blueprint $table) {
            $table->id();
            $table->text('detalle_informacion')->nullable()->comment('detalle de la informacion');
            $table->text('detalle_documentacion')->nullable()->comment('detalle de la documentacion');
            $table->string('ultima_foja', 50)->nullable();
            $table->decimal('gastos',10,2)->nullable()->comment('monto que gasto el procurador');
            $table->decimal('saldo',10,2)->nullable()->comment('saldo que queda, monto presupuesto menos gastos');
            $table->text('detalle_gasto')->nullable()->comment('detalle del gasto');
            $table->timestamp('fecha_descarga')->nullable()->comment('fecha y hora de la descarga');
            $table->decimal('compra_judicial',10,2)->nullable()->comment('monto gastado en servicios juridicos');
            $table->integer('es_validado')->comment('0 no es validado, 1 es validado por el contador, al recibir los saldos');
            $table->integer('orden_id')->comment('id de la tabla ordens');
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
        Schema::dropIfExists('procuraduria_descargas');
    }
};

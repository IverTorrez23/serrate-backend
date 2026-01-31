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
        Schema::create('causas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->text('observacion')->nullable();
            $table->text('objetivos')->nullable();
            $table->text('estrategia')->nullable();
            $table->text('informacion')->nullable();
            $table->text('apuntes_juridicos')->nullable();
            $table->text('apuntes_honorarios')->nullable();
            $table->integer('tiene_billetera')->comment('1 tiene billetera, 0 no tiene billetera');
            $table->decimal('billetera', 10, 2)->nullable();
            $table->decimal('saldo_devuelto', 10, 2)->nullable()->comment('saldo que se devuelve una vez finalizada la causa');
            $table->string('color', 10)->nullable()->comment('color en exadecimal');
            $table->integer('materia_id')->comment('id de la tabla materias');
            $table->integer('tipolegal_id')->comment('id de la tabla tipo_legals');
            $table->integer('categoria_id')->comment('id de la tabla categoria');
            $table->integer('abogado_id')->comment('id del usuario abogado');
            $table->integer('procurador_id')->comment('id del usuario procurador, por defecto es el procurador maestro');
            $table->integer('usuario_id')->comment('id del usuario dueño de la causa');
            $table->integer('plantilla_id')->nullable()->comment('id de la tabla avance_plantillas, (puede ser null)');
            $table->string('estado', 20)->comment('estado de la causa,ACTIVA,CONGELADA,FINALIZADA');
            $table->string('motivo_congelada', 70)->nullable()->comment('motivo por el congelamiento');
            $table->integer('es_eliminado')->comment('1 es eliminado, 0 no es eliminado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('causas');
    }
};

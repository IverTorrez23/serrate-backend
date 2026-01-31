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
        Schema::create('causa_postas', function (Blueprint $table) {
            $table->comment('Copia de las postas de una plantilla para una causa');
            $table->id();
            $table->text('nombre');
            $table->integer('numero_posta')->comment('numero consecutivo');
            $table->string('copia_nombre_plantilla', 300)->comment('copia del nombre de la plantilla');
            $table->integer('tiene_informe')->comment('1  (tiene su informe), 0 no tiene informe en la tabla informe_postas');
            $table->integer('causa_id')->comment('id de la tabla causa');
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
        Schema::dropIfExists('causa_postas');
    }
};

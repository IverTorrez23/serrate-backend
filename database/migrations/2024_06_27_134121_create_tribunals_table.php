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
        Schema::create('tribunals', function (Blueprint $table) {
            $table->id();
            $table->string('expediente', 50)->nullable();
            $table->string('codnurejianuj', 50)->nullable();
            $table->text('link_carpeta')->nullable()->comment('Nombre de la carpeta creada apartir del codigo de la causa y nombre del juzgado y id del tribunal');
            $table->integer('clasetribunal_id')->comment('id de la tabla clase_tribunals');
            $table->integer('causa_id')->comment('id de la tabla causa');
            $table->integer('juzgado_id')->comment('id de la tabla juzgados');
            $table->integer('tribunal_dominante')->comment('1 es dominante, 0 no es dominandte');
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
        Schema::dropIfExists('tribunals');
    }
};

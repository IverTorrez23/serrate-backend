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
        Schema::create('juzgados', function (Blueprint $table) {
            $table->id();
            $table->integer('nombre_numerico');
            $table->string('jerarquia', 50);
            $table->string('materia_juzgado', 50);
            $table->string('coordenadas', 200)->nullable();
            $table->string('foto_url', 200)->nullable();
            $table->string('contacto1', 100)->nullable();
            $table->string('contacto2', 100)->nullable();
            $table->string('contacto3', 100)->nullable();
            $table->string('contacto4', 100)->nullable();
            $table->integer('distrito_id');
            $table->integer('piso_id');
            $table->string('estado', 10)->comment('ACTIVO, INACTIVO');
            $table->integer('es_eliminado')->comment('1 es eliminado, 0 no es eliminado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juzgados');
    }
};

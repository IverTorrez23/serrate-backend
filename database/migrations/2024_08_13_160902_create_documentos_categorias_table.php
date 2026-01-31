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
        Schema::create('documentos_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->comment('nombre de la categoria de documentos');
            $table->string('tipo', 50)->comment('tipo de la categoria TRAMITES, NORMAS');
            $table->integer('categoria_id')->nullable()->comment('id de la misma tabla para saber si es subcategoria que pertenece a otra categoria');
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
        Schema::dropIfExists('documentos_categorias');
    }
};

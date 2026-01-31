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
        Schema::create('cuerpo_expedientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->nullable();
            $table->text('link_cuerpo')->nullable()->comment('link del expediente');
            $table->integer('tribunal_id')->comment('id de la tabla tribunals');
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
        Schema::dropIfExists('cuerpo_expedientes');
    }
};

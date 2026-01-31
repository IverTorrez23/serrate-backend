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
        Schema::create('tabla_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('caja_contador', 10, 2)->nullable();
            $table->decimal('deuda_extarna', 10, 2)->nullable();
            $table->decimal('caja_admin', 10, 2)->nullable();
            $table->decimal('ganancia_procesal_procuraduria', 10, 2)->nullable()->comment('es el sumado de ganacias de procuraduria y procesal');
            $table->text('titulo_index')->nullable()->comment('Titulo del texto del index');
            $table->text('texto_index')->nullable()->comment('texto informativo de la pagina index');
            $table->text('imagen_index')->nullable()->comment('imagen de index de la pagina');
            $table->text('imagen_logo')->nullable()->comment('imagen del logo del sistema');
            $table->string('nombre', 100)->comment('nombre del archivo');
            $table->text('archivo_url', 300)->comment('url del archivo');
            $table->text('url_acuerdo_lider', 300)->comment('url del acuerdo pdf del abogado lider');
            $table->text('url_acuerdo_indep', 300)->comment('url del acuerdo pdf del abogado independiente');
            $table->text('url_acuerdo_proc', 300)->comment('url del acuerdo pdf del procurador');
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
        Schema::dropIfExists('tabla_configs');
    }
};

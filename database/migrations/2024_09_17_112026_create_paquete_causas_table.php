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
        Schema::create('paquete_causas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_inicio')->nullable()->comment('fecha de inicio de vigencia (valor de la tabla compra_paquetes)');
            $table->timestamp('fecha_fin')->nullable()->comment('fecha fin de vigencia del paquete (valor de la tabla compra_paquetes)');
            $table->integer('compra_paquete_id')->comment('id de la tabla compra_paquetes');
            $table->integer('causa_id')->comment('causa asociada al paquete comprado');
            $table->timestamp('fecha_asociacion')->nullable()->comment('fecha que se asocio la causa con el paquete comprado)');
            $table->integer('usuario_id')->comment('usuario que asocio la causa el paquete comprado, de la tabla user');
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
        Schema::dropIfExists('paquete_causas');
    }
};

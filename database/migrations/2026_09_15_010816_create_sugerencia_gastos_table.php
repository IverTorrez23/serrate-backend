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
        Schema::create('sugerencia_gastos', function (Blueprint $table) {
            $table->id();
            $table->string('detalle', 300)->nullable()->comment('detalle de la sugerencia de gasto');
            $table->decimal('monto', 10, 2)->comment('monto de la sugerencia');
            $table->integer('orden_id')->nullable()->comment('id de la orden para la que fue creada (no siempre)');
            $table->integer('usuario_id')->comment('id de la tabla user, quien creo el registro');
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
        Schema::dropIfExists('sugerencia_gastos');
    }
};

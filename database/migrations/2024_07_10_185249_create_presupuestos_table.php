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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto',10,2)->nullable()->comment('monto presupuestado por el contador');
            $table->text('detalle_presupuesto')->nullable()->comment('detalle del presupuesto');
            $table->timestamp('fecha_presupuesto')->nullable()->comment('fecha y hora del presupuesto');
            $table->timestamp('fecha_entrega')->nullable()->comment('fecha y hora de entrega del presupuesto');
            $table->integer('contador_id')->comment('id del usuario contador');
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
        Schema::dropIfExists('presupuestos');
    }
};

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
        Schema::create('cotizacions', function (Blueprint $table) {
            $table->id();
            $table->decimal('compra',10,2)->nullable()->comment('monto para pago a procurador');
            $table->decimal('venta',10,2)->nullable()->comment('monto cobrado al cliente para pagar al procurador');
            $table->decimal('penalizacion',10,2)->nullable()->comment('monto de penalizacion al procurador');
            $table->integer('prioridad')->nullable()->comment('priridad de la orden');
            $table->integer('condicion')->nullable()->comment('condicion de la orden');
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
        Schema::dropIfExists('cotizacions');
    }
};

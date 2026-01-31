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
        Schema::create('matriz_cotizacions', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_prioridad')->comment('numero de la prioridad');
            $table->decimal('precio_compra',10,2)->nullable()->comment('monto para pago a procurador');
            $table->decimal('precio_venta',10,2)->nullable()->comment('monto cobrado al cliente para pagar el procurador');
            $table->decimal('penalizacion',10,2)->nullable()->comment('penalizacion para el procurador');
            $table->integer('condicion')->comment('condicion depende de los rangos de hora');
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
        Schema::dropIfExists('matriz_cotizacions');
    }
};

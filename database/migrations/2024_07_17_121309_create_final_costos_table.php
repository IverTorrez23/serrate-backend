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
        Schema::create('final_costos', function (Blueprint $table) {
            $table->id();
            $table->decimal('costo_procuraduria_compra',10,2)->nullable()->comment('monto que se pagara al procurador por la orden');
            $table->decimal('costo_procuraduria_venta',10,2)->nullable()->comment('monto que se cobrara al cliente para pagar al procurador');
            $table->decimal('costo_procesal_compra',10,2)->nullable()->comment('monto del gasto procesal en la descarga del procurador ');
            $table->decimal('costo_procesal_venta',10,2)->nullable()->comment('monto que se cobrara al cliente por el gasto procesal de la descarga (Modificado por el admin al validar)');
            $table->decimal('total_egreso',10,2)->nullable()->comment('egreso total para el cliente, suma de costo_procuraduria_venta mas costo_procesal_venta');
            $table->decimal('penalidad',10,2)->nullable()->comment('monto de la penalidad para el procurador, en caso de que la orden se califique INSUFICIENTE');
            $table->integer('es_validado')->comment('1 validado por el ADMINISTRADOR (coloco el gasto procesal), 0 no esta validado');
            $table->integer('cancelado_procurador')->comment('1 se cancelo al procurador, 0 aun no se cancela al procurador');
            $table->decimal('ganancia_procuraduria',10,2)->nullable()->comment('monto de ganancia para el sistema de procuraduria, ganancia_procuraduria = (costo_procuraduria_venta - costo_procuraduria_compra)');
            $table->decimal('ganancia_procesal',10,2)->nullable()->comment('monto de ganancia procesal para el sistema, ganancia_procesal = (costo_procesal_venta - costo_procesal_compra), (se llena cuando valida el admin)');
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
        Schema::dropIfExists('final_costos');
    }
};

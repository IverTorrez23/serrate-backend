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
        Schema::create('compra_paquetes', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto',10,2)->comment('monto pagado por el paquete');
            $table->timestamp('fecha_ini_vigencia')->nullable()->comment('fecha y hora de inicio de vigencia del paquete');
            $table->timestamp('fecha_fin_vigencia')->nullable()->comment('fecha y hora de fin de vigencia del paquete');
            $table->timestamp('fecha_compra')->nullable()->comment('fecha y hora de compra del paquete');
            $table->integer('dias_vigente')->comment('cantidad en dias que estara vigente el paquete comprado');
            $table->integer('paquete_id')->comment('id de la tabla paquetes');
            $table->integer('usuario_id')->comment('id del usuario que compro el paquete, de la tabla user');
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
        Schema::dropIfExists('detalle_compra_paquetes');
    }
};

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
        Schema::create('billetera_transaccions', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto',10,2)->comment('monto de la transaccion');
            $table->timestamp('fecha_transaccion')->nullable()->comment('fecha de la transaccion');
            $table->string('tipo', 20)->comment('tipo de transaccin, DEBITO, CREDITO');
            $table->string('glosa', 200)->comment('glosa de la transaccion, escrito por el sistema');
            $table->integer('billetera_id')->comment('id de la tabla billeteras');
            $table->integer('orden_id')->nullable()->comment('id de tabla ordens, en caso que sea un egreso de orden, puede ser null');
            $table->integer('usuario_id')->comment('id de la tabla users, usuario quien hizo la transaccion');
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
        Schema::dropIfExists('billetera_transaccions');
    }
};

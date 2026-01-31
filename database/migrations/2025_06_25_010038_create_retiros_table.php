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
        Schema::create('retiros', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto', 10, 2)->comment('monto del retiro');
            $table->timestamp('fecha_retiro')->nullable()->comment('fecha del retiro');
            $table->string('glosa', 300)->comment('glosa del retiro');
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
        Schema::dropIfExists('retiros');
    }
};

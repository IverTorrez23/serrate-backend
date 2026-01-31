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
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id();
            $table->integer('tipo')->nullable()->comment('1 = email, 2 not. push');
            $table->string('evento', 100)->nullable()->comment('evento donde se hace la notificacion');
            $table->string('emisor', 50)->nullable()->comment('correo del emisor, si es noti. por email');
            $table->string('nombre_emisor', 50)->nullable()->comment('descripcion del emisor, si es noti. por email');
            $table->integer('tipo_receptor')->nullable()->comment('1 = dinamico, 2 = estatico');
            $table->string('receptor_estatico', 50)->nullable()->comment('correo del receptor, si es noti. por email y tipo_receptor sea estatico');
            $table->string('descripcion_receptor_estatico', 100)->nullable()->comment('descripcion del receptor, si es noti. por email y tipo_receptor sea estatico');
            $table->string('asunto', 50)->nullable()->comment('Asunto de la notificacion');
            $table->integer('envia_notificacion')->nullable()->comment('1 = envia, 2 = no envia');
            $table->text('texto')->nullable()->comment('contenido de la notificacion');
            $table->integer('usuario_id')->comment('id de la tabla users, usuario quien hizo el registro');
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
        Schema::dropIfExists('notificacions');
    }
};

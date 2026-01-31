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
        Schema::create('informe_postas', function (Blueprint $table) {
            $table->id();
            $table->string('foja_informe', 20);
            $table->timestamp('fecha_informe')->nullable();
            $table->decimal('calculo_gasto',10,2)->nullable();
            $table->text('honorario_informe')->nullable();
            $table->string('foja_truncamiento', 20)->nullable();
            $table->timestamp('fecha_truncamiento')->nullable();
            $table->text('honorario_informe_truncamiento')->nullable();
            $table->integer('esta_escrito')->nullable()->comment('1 esta escrito, 0 no esta escrito');
            $table->integer('tipoposta_id')->comment('id de la tabla tipo_postas');
            $table->integer('causaposta_id')->comment('id de la tabla causa_postas');
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
        Schema::dropIfExists('informe_postas');
    }
};

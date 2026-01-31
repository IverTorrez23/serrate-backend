<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Constants\TipoUsuario;
use App\Models\Paquete;
use Carbon\Carbon;

class PaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();

        $fecha = Carbon::now('America/La_Paz')->toDateString();
        Paquete::create([
            'nombre' => 'Paquete Principiante',
            'precio' => 200,
            'cantidad_dias' => 30,
            'descripcion' => 'Paquete unipersonal por dos meses',
            'fecha_creacion'=> $fechaHora,
            'usuario_id'=> 1,
            'tiene_fecha_limite' => 1,
            'fecha_limite_compra' => $fecha,
            'tipo' => TipoUsuario::ABOGADO_INDEPENDIENTE,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Paquete::create([
            'nombre' => 'Paquete platino',
            'precio' => 350,
            'cantidad_dias' => 60,
            'descripcion' => 'Paquete unipersonal por tres meses',
            'fecha_creacion'=> $fechaHora,
            'usuario_id'=> 1,
            'tiene_fecha_limite' => 0,
            'fecha_limite_compra' => null,
            'tipo' => TipoUsuario::ABOGADO_INDEPENDIENTE,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Paquete::create([
            'nombre' => 'Paquete Oro',
            'precio' => 500,
            'cantidad_dias' => 60,
            'descripcion' => 'Paquete unipersonal por 5 meses',
            'fecha_creacion'=> $fechaHora,
            'usuario_id'=> 1,
            'tiene_fecha_limite' => 0,
            'fecha_limite_compra' => null,
            'tipo' => TipoUsuario::ABOGADO_LIDER,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

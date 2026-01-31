<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\MatrizCotizacion;

class MatrizCotizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Condicion 1 (mas de 96 horas)
        MatrizCotizacion::create([
            'numero_prioridad' => 1,
            'precio_compra' => 90,
            'precio_venta'=> 200,
            'penalizacion'=> -100,
            'condicion'=> 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 2,
            'precio_compra' => 30,
            'precio_venta'=> 85,
            'penalizacion'=> -44,
            'condicion'=> 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 3,
            'precio_compra' => 15,
            'precio_venta'=> 50,
            'penalizacion'=> -20,
            'condicion'=> 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        //Condicion 2 (de 24 a 96 horas)
        MatrizCotizacion::create([
            'numero_prioridad' => 1,
            'precio_compra' => 95,
            'precio_venta'=> 200,
            'penalizacion'=> -100,
            'condicion'=> 2,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 2,
            'precio_compra' => 32,
            'precio_venta'=> 95,
            'penalizacion'=> -47,
            'condicion'=> 2,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 3,
            'precio_compra' => 20,
            'precio_venta'=> 55,
            'penalizacion'=> -20,
            'condicion'=> 2,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        //Condicion 3 (de 8 a 24 horas)
        MatrizCotizacion::create([
            'numero_prioridad' => 1,
            'precio_compra' => 100,
            'precio_venta'=> 200,
            'penalizacion'=> -100,
            'condicion'=> 3,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 2,
            'precio_compra' => 45,
            'precio_venta'=> 105,
            'penalizacion'=> -50,
            'condicion'=> 3,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 3,
            'precio_compra' => 26,
            'precio_venta'=> 65,
            'penalizacion'=> -26,
            'condicion'=> 3,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        //Condicion 4 (de 3 a 8 horas)
        MatrizCotizacion::create([
            'numero_prioridad' => 1,
            'precio_compra' => 105,
            'precio_venta'=> 200,
            'penalizacion'=> -100,
            'condicion'=> 4,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 2,
            'precio_compra' => 50,
            'precio_venta'=> 115,
            'penalizacion'=> -53,
            'condicion'=> 4,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 3,
            'precio_compra' => 30,
            'precio_venta'=> 80,
            'penalizacion'=> -30,
            'condicion'=> 4,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        //Condicion 5 (de 1 a 3 horas)
        MatrizCotizacion::create([
            'numero_prioridad' => 1,
            'precio_compra' => 110,
            'precio_venta'=> 200,
            'penalizacion'=> -100,
            'condicion'=> 5,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 2,
            'precio_compra' => 50,
            'precio_venta'=> 125,
            'penalizacion'=> -56,
            'condicion'=> 5,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 3,
            'precio_compra' => 33,
            'precio_venta'=> 85,
            'penalizacion'=> -33,
            'condicion'=> 5,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        //Condicion 6 (de 0 a 1 hora)
        MatrizCotizacion::create([
            'numero_prioridad' => 1,
            'precio_compra' => 115,
            'precio_venta'=> 200,
            'penalizacion'=> -100,
            'condicion'=> 6,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 2,
            'precio_compra' => 50,
            'precio_venta'=> 135,
            'penalizacion'=> -59,
            'condicion'=> 6,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        MatrizCotizacion::create([
            'numero_prioridad' => 3,
            'precio_compra' => 36,
            'precio_venta'=> 90,
            'penalizacion'=> -36,
            'condicion'=> 6,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

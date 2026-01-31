<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\Distrito;

class DistritoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado=Estado::ACTIVO;
        Distrito::create([
            'nombre' => 'Santa Cruz de la Sierra',
            'abreviatura' => 'SCZ',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
        Distrito::create([
            'nombre' => 'Warnes',
            'abreviatura' => 'WAR',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
        Distrito::create([
            'nombre' => 'Montero',
            'abreviatura' => 'MON',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
    }
}

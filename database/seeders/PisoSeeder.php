<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\Piso;

class PisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado=Estado::ACTIVO;
        Piso::create([
            'nombre' => 'Palacio 18',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);

        Piso::create([
            'nombre' => 'Palacio 17',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
        Piso::create([
            'nombre' => 'Palacio 16',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
    }
}

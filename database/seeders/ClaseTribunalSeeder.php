<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\ClaseTribunal;

class ClaseTribunalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado=Estado::ACTIVO;
        ClaseTribunal::create([
            'nombre' => 'Ad Quo',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
        ClaseTribunal::create([
            'nombre' => 'Ad Quem',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
        ClaseTribunal::create([
            'nombre' => 'Ad Quem 1',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
    }
}

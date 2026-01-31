<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\Tribunal;

class TribunalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado = Estado::ACTIVO;
        Tribunal::create([
            'expediente' => '219/2017',
            'codnurejianuj' => '70307424',
            'link_carpeta' => 'C-EXTRAORD-1[1-Público Mixto]-1',
            'clasetribunal_id' => 1,
            'causa_id' => 1,
            'juzgado_id' => 1,
            'tribunal_dominante' => 1,
            'estado' => $estado,
            'es_eliminado' => 0
        ]);
    }
}

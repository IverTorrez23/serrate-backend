<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\AvancePlantilla;

class AvancePlantillaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AvancePlantilla::create([
            'nombre' => 'C - Conciliatorio/Ordinario',
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

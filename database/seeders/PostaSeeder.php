<?php

namespace Database\Seeders;

use App\Models\Posta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;

class PostaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Posta::create([
            'nombre' => 'Ingreso de Demanda Conciliatoria',
            'numero_posta'=>1,
            'plantilla_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Posta::create([
            'nombre' => 'Audiencia de Conciliación',
            'numero_posta'=>2,
            'plantilla_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Posta::create([
            'nombre' => 'Resolución Final de la Audiencia Conciliatoria',
            'numero_posta'=>3,
            'plantilla_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Posta::create([
            'nombre' => 'Ingreso de la Demanda Ordinaria',
            'numero_posta'=>4,
            'plantilla_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\CausaPosta;

class CausaPostaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CausaPosta::create([
            'nombre' => 'Ingreso de Demanda Conciliatoria',
            'numero_posta'=>1,
            'copia_nombre_plantilla'=>'C - Conciliatorio/Ordinario',
            'tiene_informe'=>1,
            'causa_id' => 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        CausaPosta::create([
            'nombre' => 'Audiencia de Conciliación',
            'numero_posta'=>2,
            'copia_nombre_plantilla'=>'C - Conciliatorio/Ordinario',
            'tiene_informe'=>1,
            'causa_id' => 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        CausaPosta::create([
            'nombre' => 'Resolución Final de la Audiencia Conciliatoria',
            'numero_posta'=>3,
            'copia_nombre_plantilla'=>'C - Conciliatorio/Ordinario',
            'tiene_informe'=>0,
            'causa_id' => 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        CausaPosta::create([
            'nombre' => 'Ingreso de la Demanda Ordinaria',
            'numero_posta'=>4,
            'copia_nombre_plantilla'=>'C - Conciliatorio/Ordinario',
            'tiene_informe'=>0,
            'causa_id' => 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

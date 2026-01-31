<?php

namespace Database\Seeders;

use App\Models\TipoLegal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\Estado;

class TipoLegalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado=Estado::ACTIVO;
        TipoLegal::create([
              'nombre'=>'Ordinario',
              'abreviatura'=>'ORD',
              'estado'=>$estado,
             'es_eliminado'=>0,
             'materia_id'=>1
        ]);

        TipoLegal::create([
            'nombre'=>'Extraordinario',
             'abreviatura'=>'EXTRAORD',
             'estado'=>$estado,
             'es_eliminado'=>0,
             'materia_id'=>2
      ]);

      TipoLegal::create([
        'nombre'=>'Procedimiento Laboral Común (art.61 CPT)',
             'abreviatura'=>'PLC',
             'estado'=>$estado,
             'es_eliminado'=>0,
             'materia_id'=>3
        ]);

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Materia;
use App\Constants\Estado;



class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $estado=Estado::ACTIVO;
        Materia::create([
            'nombre' => 'Civil Comercial',
            'abreviatura' => 'C',
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);

        Materia::create([
            'nombre'=>'Familia',
             'abreviatura'=>'F',
             'estado'=>$estado,
             'es_eliminado'=>0
        ]);

        Materia::create([
            'nombre'=>'Trabajo o Laboral',
             'abreviatura'=>'T',
             'estado'=>$estado,
             'es_eliminado'=>0
        ]);

    }
}

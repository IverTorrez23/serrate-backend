<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\Juzgado;

class JuzgadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado=Estado::ACTIVO;
        Juzgado::create([
              'nombre_numerico'=>1,
              'jerarquia'=>'Público Mixto',
              'materia_juzgado'=>'Civil-Familia-Penal',
              'coordenadas'=>'https://maps.google.com/?q=-17.509714738558415,-63.165620546518724',
              'foto_url'=>'WAR juzgado.jpg',
              'contacto1'=>'persona 1',
              'contacto2'=>'persona 2',
              'contacto3'=>'persona 3',
              'contacto4'=>'persona 4',
              'distrito_id'=>1,
              'piso_id'=>1,
              'estado'=>$estado,
             'es_eliminado'=>0
        ]);
        Juzgado::create([
            'nombre_numerico'=>2,
            'jerarquia'=>'Conciliador',
            'materia_juzgado'=>'Civil',
            'coordenadas'=>'https://maps.google.com/?q=-17.509714738558415,-63.165620546518724',
            'foto_url'=>'WAR juzgado.jpg',
            'contacto1'=>'persona 1',
            'contacto2'=>'persona 2',
            'contacto3'=>'persona 3',
            'contacto4'=>'persona 4',
            'distrito_id'=>2,
            'piso_id'=>2,
            'estado'=>$estado,
           'es_eliminado'=>0
      ]);
    }
}

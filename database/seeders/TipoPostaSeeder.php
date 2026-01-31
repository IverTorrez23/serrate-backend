<?php

namespace Database\Seeders;

use App\Models\TipoPosta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;

class TipoPostaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TipoPosta::create([
            'nombre'=>'Perención de Instancia',
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
        ]);
        TipoPosta::create([
            'nombre'=>'Conciliación',
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
        ]);
        TipoPosta::create([
            'nombre'=>'Transacción',
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
        ]);
        TipoPosta::create([
            'nombre'=>'Desistimiento',
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
        ]);
        TipoPosta::create([
            'nombre'=>'Extinción por Inactividad',
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
        ]);
    }
}

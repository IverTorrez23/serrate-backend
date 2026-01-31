<?php

namespace Database\Seeders;

use App\Models\InformePosta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use Carbon\Carbon;

class InformePostaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        InformePosta::create([
            'foja_informe' => 'Exp Conc.38',
            'fecha_informe' => $fechaHora,
            'calculo_gasto' => 0,
            'honorario_informe' => 'informe de honorario',
            'foja_truncamiento' => 'exp/45',
            'fecha_truncamiento' => $fechaHora,
            'honorario_informe_truncamiento' => 'honorario',
            'esta_escrito'=>0,
            'tipoposta_id' => 1,
            'causaposta_id' => 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        InformePosta::create([
            'foja_informe' => 'Exp Conc.40',
            'fecha_informe' => $fechaHora,
            'calculo_gasto' => 0,
            'honorario_informe' => 'informe de honorario 2',
            'foja_truncamiento' => 'exp/45',
            'fecha_truncamiento' => $fechaHora,
            'honorario_informe_truncamiento' => 'honorario 2',
            'esta_escrito'=>0,
            'tipoposta_id' => 1,
            'causaposta_id' => 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\Deposito;
use Carbon\Carbon;

class DepositoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        Deposito::create([
            'fecha_deposito' => $fechaHora,
            'detalle_deposito'=>'detalle del deposito',
            'monto'=>10,
            'tipo'=>'DESPOSITO',
            'causa_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Deposito::create([
            'fecha_deposito' => $fechaHora,
            'detalle_deposito'=>'segundo detalle del deposito',
            'monto'=>10,
            'tipo'=>'DESPOSITO',
            'causa_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

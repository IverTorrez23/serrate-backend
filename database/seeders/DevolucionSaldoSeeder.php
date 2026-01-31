<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Constants\Estado;
use App\Models\DevolucionSaldo;

class DevolucionSaldoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        DevolucionSaldo::create([
            'fecha_devolucion' => $fechaHora,
            'detalle_devolucion'=>'detalle de la devolucion',
            'monto'=>10,
            'causa_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

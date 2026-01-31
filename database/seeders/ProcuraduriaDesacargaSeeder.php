<?php

namespace Database\Seeders;

use App\Models\ProcuraduriaDescarga;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use Carbon\Carbon;

class ProcuraduriaDesacargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        ProcuraduriaDescarga::create([
            'detalle_informacion' => 'informacion descargada',
            'detalle_documentacion'=>'documentacion descargada',
            'ultima_foja'=>'12mf',
            'gastos'=>90,
            'saldo'=>10,
            'detalle_gasto'=>'se gasto en un memorial',
            'fecha_descarga'=>$fechaHora,
            'compra_judicial'=>90,
            'es_validado'=>0,
            'orden_id'=>1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}

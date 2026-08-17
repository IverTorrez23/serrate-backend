<?php

namespace App\Services;

use App\Constants\EstadoCupon;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CuponService
{
    public function store($data)
    {
        $cupon = Cupon::create([
            'codigo' => $data['codigo'],
            'fecha_uso' => $data['fecha_uso'],
            'paquete_id' => $data['paquete_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => EstadoCupon::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $cupon;
    }
    public function update($data, $cuponId)
    {
        $cupon = Cupon::findOrFail($cuponId);
        $cupon->update($data);
        return $cupon;
    }
    public function obtenerUno($cuponId)
    {
        $cupon = Cupon::findOrFail($cuponId);
        return $cupon;
    }
    public function listarActivos()
    {
        $cupon = Cupon::where('estado', EstadoCupon::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $cupon;
    }
    public function destroy(Cupon $cupon)
    {
        $cupon->es_eliminado = 1;
        $cupon->estado = EstadoCupon::INACTIVO;
        $cupon->save();
        return $cupon;
    }
    public function generarCodigoUnico(int $longitud = 6): string
    {
        $caracteres = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';

            for ($i = 0; $i < $longitud; $i++) {
                $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }
        } while (Cupon::where('codigo', $codigo)->exists());

        return $codigo;
    }
    public function canjearCupon(
        string $codigo,
        int $paqueteId,
        int $usuarioId
    ): Cupon {
        return DB::transaction(function () use (
            $codigo,
            $paqueteId,
            $usuarioId
        ) {
            $cupon = Cupon::query()
                ->where('codigo', $codigo)
                ->where('paquete_id', $paqueteId)
                ->where('es_eliminado', 0)
                ->lockForUpdate()
                ->first();

            if (!$cupon) {
                throw new ModelNotFoundException(
                    'El cupón ingresado no existe o no corresponde a este paquete.'
                );
            }

            if ($cupon->estado !== EstadoCupon::ACTIVO) {
                throw new \RuntimeException(
                    'El cupón ingresado no se encuentra disponible o ya fue canjeado.'
                );
            }

            if ($cupon->fecha_uso !== null) {
                throw new \RuntimeException(
                    'Este cupón ya fue utilizado anteriormente.'
                );
            }

            if ((int) $cupon->usuario_id !== 0) {
                throw new \RuntimeException(
                    'Este cupón ya se encuentra asociado a otro usuario.'
                );
            }

            $cupon->fecha_uso = now('America/La_Paz');
            $cupon->usuario_id = $usuarioId;
            $cupon->estado = EstadoCupon::USADO;

            $cupon->save();

            return $cupon->fresh();
        });
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Orden;
use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ListaOrdenVencidasLevesService
{
    public function getOrdenesDeCausaVencidasLevesGeneral(Request $request, $idCausa)
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        try {
            $query = Orden::select([
                'id',
                'entrega_informacion',
                'entrega_documentacion',
                'fecha_inicio',
                'fecha_fin',
                'fecha_giro',
                'plazo_hora',
                'fecha_recepcion',
                'etapa_orden',
                'calificacion',
                'prioridad',
                'fecha_cierre',
                'girada_por',
                'fecha_ini_bandera',
                'notificado',
                'lugar_ejecucion',
                'sugerencia_presupuesto',
                'tiene_propina',
                'propina',
                'causa_id',
                'procurador_id',
                'matriz_id',
                'estado',
            ])
                ->with([
                    'causa:id,nombre',
                    'procurador:id,name,email,tipo,estado',
                    'procurador.persona:usuario_id,nombre,apellido,telefono,direccion',
                    'matriz:id,numero_prioridad,precio_compra,penalizacion',
                    'cotizacion:id,compra,venta,penalizacion,prioridad,condicion,orden_id',
                    'descarga:id,compra_judicial,saldo,fecha_descarga,orden_id',
                    'finalCostos:id,costo_procesal_venta,costo_procuraduria_compra,costo_procuraduria_venta,total_egreso,orden_id',
                    'presupuesto:id,monto,detalle_presupuesto,fecha_presupuesto,fecha_entrega,orden_id'

                ])
                ->active();

            if ($idCausa) {
                $query->where('causa_id', $idCausa);
                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
                $query->where('fecha_fin', '<=', $fechaHora)
                    ->whereHas('descarga', function ($query) {
                        $query->whereHas('confirmacion', function ($query) {
                            $query->where('confir_sistema', 1);
                        });
                    });
            }

            if ($request->has('search')) {
                $search = json_decode($request->input('search'), true);
                $query->search($search);
            }

            if ($request->has('sort')) {
                $sort = json_decode($request->input('sort'), true);
                $query->sort($sort);
            }

            $perPage = $request->input('perPage', 10);
            return $query->paginate($perPage);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }
    public function getOrdenesVencidasLevesDeCausaProcurador(Request $request, $idCausa)
    {
        $now = Carbon::now('America/La_Paz');
        $fechaHora = $now->format('Y-m-d H:i:00');
        $procuradorId = Auth::user()->id;
        try {
            $query = Orden::select([
                'id',
                'entrega_informacion',
                'entrega_documentacion',
                'fecha_inicio',
                'fecha_fin',
                'fecha_giro',
                'plazo_hora',
                'fecha_recepcion',
                'etapa_orden',
                'calificacion',
                'prioridad',
                'fecha_cierre',
                'girada_por',
                'fecha_ini_bandera',
                'notificado',
                'lugar_ejecucion',
                'sugerencia_presupuesto',
                'tiene_propina',
                'propina',
                'causa_id',
                'procurador_id',
                'matriz_id',
                'estado',
            ])
                ->with([
                    'causa:id,nombre',
                    'procurador:id,name,email,tipo,estado',
                    'procurador.persona:usuario_id,nombre,apellido,telefono,direccion',
                    'matriz:id,numero_prioridad,precio_compra,penalizacion',
                    'cotizacion:id,compra,venta,penalizacion,prioridad,condicion,orden_id',
                    'descarga:id,compra_judicial,orden_id',
                    'finalCostos:id,costo_procesal_venta,costo_procuraduria_compra,costo_procuraduria_venta,total_egreso,orden_id'

                ])
                ->active();

            if ($idCausa) {
                $query->where('causa_id', $idCausa);
                $query->where('procurador_id', $procuradorId);
                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
                $query->where('fecha_fin', '<=', $fechaHora)
                    ->whereHas('descarga', function ($query) {
                        $query->whereHas('confirmacion', function ($query) {
                            $query->where('confir_sistema', 1);
                        });
                    });
            }

            if ($request->has('search')) {
                $search = json_decode($request->input('search'), true);
                $query->search($search);
            }

            if ($request->has('sort')) {
                $sort = json_decode($request->input('sort'), true);
                $query->sort($sort);
            }

            $perPage = $request->input('perPage', 10);
            return $query->paginate($perPage);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }

    public function devuelveListaOrdenVencidasLeves(Request $request, $idCausa)
    {
        $tipoUsuario = Auth::user()->tipo;
        if ($tipoUsuario === TipoUsuario::PROCURADOR) {
            $ordenes = $this->getOrdenesVencidasLevesDeCausaProcurador($request, $idCausa);
        } else {
            $ordenes = $this->getOrdenesDeCausaVencidasLevesGeneral($request, $idCausa);
        }
        return $ordenes;
    }
}

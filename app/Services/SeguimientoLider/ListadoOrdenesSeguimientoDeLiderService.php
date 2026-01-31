<?php

namespace App\Services\SeguimientoLider;

use Carbon\Carbon;
use App\Models\Orden;
use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use Illuminate\Support\Facades\Auth;

class ListadoOrdenesSeguimientoDeLiderService
{
    public function getOrdenesDeCausaGiradasDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('etapa_orden', EtapaOrden::GIRADA);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
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
            return  $query->paginate($perPage);
    
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }
    public function getOrdenesDeCausaPrePresupuestadasDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('etapa_orden', EtapaOrden::PREPRESUPUESTADA);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
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
    public function getOrdenesDeCausaPresupuestadasDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('etapa_orden', EtapaOrden::PRESUPUESTADA);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
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

    public function getOrdenesDeCausaAceptadasDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('etapa_orden', EtapaOrden::ACEPTADA);
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('estado', Estado::ACTIVO);
                $query->where('es_eliminado', 0)
                    ->whereHas('presupuesto', function ($query) {
                        $query->whereNull('fecha_entrega');
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
    public function getOrdenesDeCausaDineroEntregadoDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('etapa_orden', EtapaOrden::DINERO_ENTREGADO);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
                $query->whereNull('fecha_recepcion');
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
    public function getOrdenesDeCausaListaRealizarDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('estado', Estado::ACTIVO);
                $query->where('es_eliminado', 0);
                $query->whereNotNull('fecha_recepcion');
                $query->whereDoesntHave('descarga')
                    ->whereHas('presupuesto', function ($query) {
                        $query->whereNotNull('fecha_entrega');
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
    public function getOrdenesDeCausaDescargadasDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('etapa_orden', EtapaOrden::DESCARGADA);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
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
    public function getOrdenesDeCausaPronuncioAbogadoGeneral(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('estado', Estado::ACTIVO);
                $query->where('es_eliminado', 0);
                $query->where('etapa_orden', EtapaOrden::PRONUNCIO_ABOGADO);
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
    public function getOrdenesDeCausaCuentaConciliadaDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('etapa_orden', EtapaOrden::PRONUNCIO_CONTADOR);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
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

    public function getOrdenesDeCausaVencidasLevesDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
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
    public function getOrdenesDeCausaVencidasGravesDeLider(Request $request, $idCausa)
    {
        $liderid = Auth::user()->id;
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
                $query->where('usuario_id', $liderid);
                $query->where('girada_por', TipoUsuario::ABOGADO_LIDER);
                $query->where('etapa_orden', '!=', EtapaOrden::CERRADA);
                $query->where('estado', Estado::ACTIVO); // Añade condiciones adicionales si es necesario
                $query->where('es_eliminado', 0);
                $query->where('fecha_fin', '<', $fechaHora);
                $query->whereDoesntHave('descarga');
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
}

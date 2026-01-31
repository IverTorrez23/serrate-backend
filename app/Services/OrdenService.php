<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\EstadoCausa;
use App\Constants\EtapaOrden;
use App\Constants\FechaHelper;
use App\Models\Causa;
use App\Models\Orden;
use Carbon\Carbon;

class OrdenService
{
    public function index($request)
    {
        return $this->getOrdenes($request);
    }

    public function listarPorCausa($request, $idCausa)
    {
        return $this->getOrdenes($request, $idCausa);
    }

    public function getOrdenes($request, $idCausa = null)
    {
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
    public function getOrdenesProcurador($request, $idCausa, $procuradorId)
    {
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
    public function getOrdenesTodosProcurador($request, $idCausa)
    {
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


    public function listarOrden(Orden $orden = null)
    {
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
                'usuario_id',
                'estado',
            ])
                ->with([
                    'causa:id,nombre,materia_id,tipolegal_id,usuario_id,abogado_id',
                    'causa.materia:id,abreviatura',
                    'causa.tipoLegal:id,abreviatura',
                    'procurador:id,name,email,tipo,estado',
                    'procurador.persona:usuario_id,nombre,apellido,telefono,direccion',
                    'matriz:id,numero_prioridad,precio_compra,penalizacion',
                    'cotizacion:id,prioridad,condicion,orden_id',
                    'descarga:id,ultima_foja,fecha_descarga,detalle_informacion,detalle_documentacion,gastos,saldo,detalle_gasto,orden_id',
                    'presupuesto:id,monto,detalle_presupuesto,fecha_presupuesto,fecha_entrega,orden_id',
                    'descarga.confirmacion:id,fecha_confir_abogado,fecha_confir_contador,justificacion_rechazo,confir_sistema,confir_abogado,confir_contador,descarga_id'
                ])
                ->active();

            if ($orden) {
                $query->where('id', $orden->id);
                $result = $query->first();

                if (!$result) {
                    return response()->json(['message' => 'Orden no encontrada.'], 404);
                }

                return [
                    'message' => 'Orden obtenida correctamente',
                    'data' => $result
                ];
            }

            $result = $query->get();

            return [
                'message' => 'Órdenes obtenidas correctamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }
    public function listarOrdenParaEntregarPresupuestoDeProcurador($procuradorId)
    {
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
                    'causa:id,nombre,materia_id,tipolegal_id',
                    'causa.materia:id,abreviatura',
                    'causa.tipoLegal:id,abreviatura',
                    'presupuesto:id,monto,detalle_presupuesto,fecha_presupuesto,fecha_entrega,orden_id',
                ])
                ->active()
                ->whereHas('presupuesto', function ($query) {
                    $query->whereNull('fecha_entrega');
                })
                ->whereDoesntHave('descarga'); //Verifica que no exista registros en descarga

            $query->where('procurador_id', $procuradorId);
            $result = $query->get();

            return [
                'message' => 'Ordenes obtenidas correctamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }
    public function listarOrdenParaDevolverPresupuestoDeProcurador($procuradorId)
    {
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
                    'causa:id,nombre,materia_id,tipolegal_id',
                    'causa.materia:id,abreviatura',
                    'causa.tipoLegal:id,abreviatura',
                    'presupuesto:id,monto,detalle_presupuesto,fecha_presupuesto,fecha_entrega,orden_id',
                    'descarga:id,detalle_informacion,detalle_gasto,gastos,saldo,es_validado,orden_id',
                    'descarga.confirmacion:id,confir_abogado,fecha_confir_abogado,justificacion_rechazo,descarga_id'
                ])
                ->active()
                ->whereHas('descarga', function ($query) {
                    $query->where('es_validado', 0)
                        ->whereHas('confirmacion', function ($query) {
                            $query->whereNull('fecha_confir_contador');
                        });
                });


            $query->where('procurador_id', $procuradorId);
            $result = $query->get();

            return [
                'message' => 'Ordenes obtenidas correctamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }
    public function listarOrdenParaColocarCostoJudicialVenta()
    {
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
                    'causa:id,nombre,materia_id,tipolegal_id',
                    'causa.materia:id,abreviatura',
                    'causa.tipoLegal:id,abreviatura',
                    'presupuesto:id,monto,detalle_presupuesto,fecha_presupuesto,fecha_entrega,orden_id',
                    'descarga:id,detalle_informacion,detalle_gasto,gastos,saldo,es_validado,orden_id',
                    'descarga.confirmacion:id,confir_abogado,fecha_confir_abogado,justificacion_rechazo,descarga_id',
                    'finalCostos:id,costo_procesal_compra,costo_procesal_venta,costo_procuraduria_compra,costo_procuraduria_venta,total_egreso,orden_id'

                ])
                ->active()
                ->whereHas('finalCostos', function ($query) {
                    $query->where('es_validado', 0);
                });
            $result = $query->get();

            return [
                'message' => 'Ordenes obtenidas correctamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }

    public function store($data)
    {
        $orden = Orden::create([
            'entrega_informacion' => $data['entrega_informacion'],
            'entrega_documentacion' => $data['entrega_documentacion'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'fecha_giro' => $data['fecha_giro'],
            'plazo_hora' => $data['plazo_hora'],
            'fecha_recepcion' => $data['fecha_recepcion'],
            'etapa_orden' => $data['etapa_orden'],
            'calificacion' => '',
            'prioridad' => $data['prioridad'],
            'fecha_cierre' => null,
            'girada_por' => $data['girada_por'],
            'fecha_ini_bandera' => $data['fecha_ini_bandera'],
            'notificado' => $data['notificado'],


            'lugar_ejecucion' => $data['lugar_ejecucion'],
            'sugerencia_presupuesto' => $data['sugerencia_presupuesto'],
            'tiene_propina' => $data['tiene_propina'],
            'propina' => $data['propina'],
            'causa_id' => $data['causa_id'],
            'procurador_id' => $data['procurador_id'],
            'matriz_id' => $data['matriz_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $orden;
    }
    public function update($data, $ordenId)
    {
        $orden = Orden::findOrFail($ordenId);
        $orden->update($data);
        return $orden;
    }
    public function destroy($ordenId)
    {
        $orden = Orden::findOrFail($ordenId);
        $orden->es_eliminado = 1;
        $orden->save();
        return $orden;
    }

    public function obtenerUno($ordenId)
    {
        $orden = Orden::findOrFail($ordenId);
        return $orden;
    }
    public function obtenerListaOrdenCerradasParaPagoProcurador($procuradorId, $fechaInicioConsulta, $fechaFinConsulta)
    {
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
                    'causa:id,nombre,materia_id,tipolegal_id',
                    'causa.materia:id,abreviatura',
                    'causa.tipoLegal:id,abreviatura',
                    'cotizacion:id,compra,penalizacion,condicion,orden_id',
                    'presupuesto:id,monto,detalle_presupuesto,fecha_presupuesto,fecha_entrega,orden_id',
                    'descarga:id,detalle_informacion,detalle_gasto,gastos,saldo,es_validado,orden_id',
                    'descarga.confirmacion:id,confir_abogado,fecha_confir_abogado,justificacion_rechazo,descarga_id',
                    'finalCostos:id,costo_procesal_compra,costo_procesal_venta,costo_procuraduria_compra,costo_procuraduria_venta,total_egreso,cancelado_procurador,orden_id'

                ])
                ->active()
                ->whereHas('finalCostos', function ($query) {
                    $query->where('cancelado_procurador', 0);
                })
                ->where('procurador_id', $procuradorId)
                ->whereBetween('fecha_cierre', [$fechaInicioConsulta, $fechaFinConsulta])
                ->where('estado', Estado::ACTIVO)
                ->where('es_eliminado', 0)
                ->where('etapa_orden', EtapaOrden::CERRADA);

            $result = $query->get();

            return [
                'message' => 'Ordenes obtenidas correctamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }
    public function usuarioTieneOrdenesNoCerradas($usuarioId): bool
    {
        return Causa::where('usuario_id', $usuarioId)
            ->where('es_eliminado', 0)
            ->where('estado', '!=', EstadoCausa::TERMINADA)
            ->whereHas('ordenes', function ($query) {
                $query->where('es_eliminado', 0)
                    ->where('etapa_orden', '!=', EtapaOrden::CERRADA);
            })
            ->exists();
    }

    //Listado de ordenes ordenados por piso de forma descendente, ordenes sin cerrar
    public function listarOrdenesActivasPorPisoProcurador($procuradorId)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
        return Orden::query()
            ->select(['id', 'entrega_informacion', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id']) // Campos de Orden
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('es_eliminado', 0)
            ->where('procurador_id', '=', $procuradorId)
            ->where('fecha_inicio', '<=', $fechaHoraSistema)
            ->whereDoesntHave('descarga')
            ->with([
                'causa' => function ($query) {
                    $query->select(['id', 'nombre', 'observacion', 'estado', 'materia_id', 'tipolegal_id', 'categoria_id', 'abogado_id', 'procurador_id'])
                        ->where('es_eliminado', 0)
                        ->with([
                            'materia:id,nombre,abreviatura', // Campos de Materia
                            'tipoLegal:id,nombre,abreviatura', // Campos de TipoLegal
                            'categoria:id,nombre,abreviatura', // Campos de categoria
                            'abogado' => function ($abogadoQuery) {
                                $abogadoQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            },
                            'procurador' => function ($procuradorQuery) {
                                $procuradorQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            },
                            'tribunales' => function ($tribunalQuery) {
                                $tribunalQuery
                                    ->select(['id', 'expediente', 'tribunal_dominante', 'causa_id', 'juzgado_id'])
                                    ->where('es_eliminado', 0)
                                    ->orderBy('id', 'asc') // Primer registro del tribunal
                                    ->limit(1)
                                    ->with([
                                        'juzgado' => function ($juzgadoQuery) {
                                            $juzgadoQuery
                                                ->select(['id', 'nombre_numerico', 'piso_id'])
                                                ->with([
                                                    'piso:id,nombre' // Campos de Piso
                                                ]);
                                        }
                                    ]);
                            }
                        ]);
                }
            ])
            ->get()
            ->sortBy(function ($orden) {
                $pisoNombre = $orden->causa
                    ->tribunales
                    ->first()?->juzgado
                    ->piso
                    ->nombre;

                // 0 si tiene piso, 1 si no
                $tienePiso = $pisoNombre ? 0 : 1;

                // Retorno doble: primero los que tienen piso, después los que no, y nombre descendente
                return [$tienePiso, $pisoNombre ? -strcmp($pisoNombre, '') : null];
            })
            ->values();
    }



    public function listarOrdenesActivasPorPiso()
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();
        return Orden::query()
            ->select(['id', 'entrega_informacion', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id']) // Campos de Orden
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('es_eliminado', 0)
            ->where('fecha_inicio', '<=', $fechaHoraSistema)
            ->whereDoesntHave('descarga')
            ->with([
                'causa' => function ($query) {
                    $query->select(['id', 'nombre', 'observacion', 'estado', 'materia_id', 'tipolegal_id', 'categoria_id', 'abogado_id', 'procurador_id'])
                        ->where('es_eliminado', 0)
                        ->with([
                            'materia:id,nombre,abreviatura', // Campos de Materia
                            'tipoLegal:id,nombre,abreviatura', // Campos de TipoLegal
                            'categoria:id,nombre,abreviatura', // Campos de categoria
                            'abogado' => function ($abogadoQuery) {
                                $abogadoQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            },
                            'procurador' => function ($procuradorQuery) {
                                $procuradorQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            },
                            'tribunales' => function ($tribunalQuery) {
                                $tribunalQuery
                                    ->select(['id', 'expediente', 'tribunal_dominante', 'causa_id', 'juzgado_id'])
                                    ->where('es_eliminado', 0)
                                    ->orderBy('id', 'asc') // Primer registro del tribunal
                                    ->limit(1)
                                    ->with([
                                        'juzgado' => function ($juzgadoQuery) {
                                            $juzgadoQuery
                                                ->select(['id', 'nombre_numerico', 'piso_id'])
                                                ->with([
                                                    'piso:id,nombre' // Campos de Piso
                                                ]);
                                        }
                                    ]);
                            }
                        ]);
                }
            ])
            ->get()
            ->sortBy(function ($orden) {
                $pisoNombre = $orden->causa
                    ->tribunales
                    ->first()?->juzgado
                    ->piso
                    ->nombre;

                // 0 si tiene piso, 1 si no
                $tienePiso = $pisoNombre ? 0 : 1;

                // Retorno doble: primero los que tienen piso, después los que no, y nombre descendente
                return [$tienePiso, $pisoNombre ? -strcmp($pisoNombre, '') : null];
            })
            ->values();
    }
    public function obtenerOrdenesPorUrgenciasProcurador($procuradorId)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();

        $ordenes = Orden::select(['id', 'entrega_informacion', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id'])
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('es_eliminado', 0)
            ->where('procurador_id', '=', $procuradorId)
            ->where('fecha_inicio', '<=', $fechaHoraSistema)
            ->whereDoesntHave('descarga')
            ->with([
                'causa' => function ($query) {
                    $query->select(['id', 'nombre', 'observacion', 'estado', 'materia_id', 'tipolegal_id', 'categoria_id', 'abogado_id', 'procurador_id'])
                        ->where('es_eliminado', 0)
                        ->with([
                            'materia:id,nombre,abreviatura', // Campos de Materia
                            'tipoLegal:id,nombre,abreviatura', // Campos de TipoLegal
                            'categoria:id,nombre,abreviatura', // Campos de categoria
                            'abogado' => function ($abogadoQuery) {
                                $abogadoQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            },
                            'procurador' => function ($procuradorQuery) {
                                $procuradorQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            }

                        ]);
                }
            ])
            ->orderByRaw("
            CASE 
                WHEN fecha_fin >= ? THEN 0 
                ELSE 1 
            END ASC
        ", [$fechaHoraSistema]) // primero no vencidas, luego vencidas
            ->orderBy('prioridad', 'asc') // prioridad 1 antes que 2, antes que 3
            ->orderBy('fecha_fin', 'asc') // entre mismas prioridad, fecha más próxima primero
            ->get();

        return $ordenes;
    }
    public function obtenerOrdenesPorUrgencias()
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();

        $ordenes = Orden::select(['id', 'entrega_informacion', 'fecha_inicio', 'fecha_fin', 'prioridad', 'causa_id'])
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('es_eliminado', 0)
            ->where('fecha_inicio', '<=', $fechaHoraSistema)
            ->whereDoesntHave('descarga')
            ->with([
                'causa' => function ($query) {
                    $query->select(['id', 'nombre', 'observacion', 'estado', 'materia_id', 'tipolegal_id', 'categoria_id', 'abogado_id', 'procurador_id'])
                        ->where('es_eliminado', 0)
                        ->with([
                            'materia:id,nombre,abreviatura', // Campos de Materia
                            'tipoLegal:id,nombre,abreviatura', // Campos de TipoLegal
                            'categoria:id,nombre,abreviatura', // Campos de categoria
                            'abogado' => function ($abogadoQuery) {
                                $abogadoQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            },
                            'procurador' => function ($procuradorQuery) {
                                $procuradorQuery
                                    ->select(['id']) // Campo necesario para la relación
                                    ->with([
                                        'persona:id,nombre,apellido,usuario_id'
                                    ]);
                            }

                        ]);
                }
            ])
            ->orderByRaw("
            CASE 
                WHEN fecha_fin >= ? THEN 0 
                ELSE 1 
            END ASC
        ", [$fechaHoraSistema]) // primero no vencidas, luego vencidas
            ->orderBy('prioridad', 'asc') // prioridad 1 antes que 2, antes que 3
            ->orderBy('fecha_fin', 'asc') // entre mismas prioridad, fecha más próxima primero
            ->get();

        return $ordenes;
    }
    public function obtenerOrdenesEjecutarProcurador($procuradorId)
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();

        return Orden::query()
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('es_eliminado', 0)
            ->where('procurador_id', '=', $procuradorId)
            ->where('fecha_inicio', '<=', $fechaHoraSistema)
            // Órdenes que no tengan descargas
            ->whereDoesntHave('descarga')

            // Ordenadas por fecha_fin ascendente
            ->orderBy('fecha_fin', 'asc')

            // Eager load relaciones necesarias
            ->with([
                'presupuesto:id,orden_id,detalle_presupuesto',
                // Causa con campos específicos
                'causa:id,nombre,observacion,materia_id,tipolegal_id',
                // Materia de la causa
                'causa.materia:id,nombre,abreviatura',

                // TipoLegal de la causa
                'causa.tipolegal:id,nombre,abreviatura',
                // Primer tribunal de la causa
                'causa.tribunales' => function ($q) {
                    $q->orderBy('id', 'asc')->limit(1);
                },

                // Última descarga de la causa (entre todas las órdenes)
                'causa.ordenes.descarga' => function ($q) {
                    $q->latest('id')->limit(1);
                }
            ])
            ->get()
            ->map(function ($orden) {
                return [
                    'id'                  => $orden->id,
                    'fecha_inicio'        => $orden->fecha_inicio,
                    'fecha_fin'           => $orden->fecha_fin,
                    'prioridad'        => $orden->prioridad,
                    'causa_id'        => $orden->causa_id,
                    'entrega_informacion' => $orden->entrega_informacion,

                    // Presupuesto
                    'detalle_presupuesto' => optional($orden->presupuesto)->detalle_presupuesto,

                    // Causa
                    'causa' => [
                        'id'          => optional($orden->causa)->id,
                        'nombre'      => optional($orden->causa)->nombre,
                        'observacion' => optional($orden->causa)->observacion,
                        // Materia
                        'materia' => $orden->causa && $orden->causa->materia ? [
                            'id'          => $orden->causa->materia->id,
                            'nombre'      => $orden->causa->materia->nombre,
                            'abreviatura' => $orden->causa->materia->abreviatura,
                        ] : null,
                        // TipoLegal
                        'tipo_legal' => $orden->causa && $orden->causa->tipolegal ? [
                            'id'          => $orden->causa->tipolegal->id,
                            'nombre'      => $orden->causa->tipolegal->nombre,
                            'abreviatura' => $orden->causa->tipolegal->abreviatura,
                        ] : null,
                    ],

                    // Tribunal
                    'expediente' => optional($orden->causa->tribunales->first())->expediente,

                    // Última foja de todas las descargas de la causa
                    'ultima_foja' => optional(
                        $orden->causa->ordenes
                            ->map->descarga // obtenemos cada descarga (hasOne)
                            ->filter()      // quitamos null (órdenes sin descarga)
                            ->sortByDesc('id')
                            ->first()
                    )->ultima_foja,
                ];
            });
    }
    public function obtenerOrdenesEjecutar()
    {
        $fechaHoraSistema = FechaHelper::fechaHoraBolivia();

        return Orden::query()
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('es_eliminado', 0)
            ->where('fecha_inicio', '<=', $fechaHoraSistema)
            // Órdenes que no tengan descargas
            ->whereDoesntHave('descarga')

            // Ordenadas por fecha_fin ascendente
            ->orderBy('fecha_fin', 'asc')

            // Eager load relaciones necesarias
            ->with([
                'presupuesto:id,orden_id,detalle_presupuesto',
                // Causa con campos específicos
                'causa:id,nombre,observacion,materia_id,tipolegal_id',
                // Materia de la causa
                'causa.materia:id,nombre,abreviatura',

                // TipoLegal de la causa
                'causa.tipolegal:id,nombre,abreviatura',
                // Primer tribunal de la causa
                'causa.tribunales' => function ($q) {
                    $q->orderBy('id', 'asc')->limit(1);
                },

                // Última descarga de la causa (entre todas las órdenes)
                'causa.ordenes.descarga' => function ($q) {
                    $q->latest('id')->limit(1);
                }
            ])
            ->get()
            ->map(function ($orden) {
                return [
                    'id'                  => $orden->id,
                    'fecha_inicio'        => $orden->fecha_inicio,
                    'fecha_fin'           => $orden->fecha_fin,
                    'prioridad'        => $orden->prioridad,
                    'causa_id'        => $orden->causa_id,
                    'entrega_informacion' => $orden->entrega_informacion,

                    // Presupuesto
                    'detalle_presupuesto' => optional($orden->presupuesto)->detalle_presupuesto,

                    // Causa
                    'causa' => [
                        'id'          => optional($orden->causa)->id,
                        'nombre'      => optional($orden->causa)->nombre,
                        'observacion' => optional($orden->causa)->observacion,
                        // Materia
                        'materia' => $orden->causa && $orden->causa->materia ? [
                            'id'          => $orden->causa->materia->id,
                            'nombre'      => $orden->causa->materia->nombre,
                            'abreviatura' => $orden->causa->materia->abreviatura,
                        ] : null,
                        // TipoLegal
                        'tipo_legal' => $orden->causa && $orden->causa->tipolegal ? [
                            'id'          => $orden->causa->tipolegal->id,
                            'nombre'      => $orden->causa->tipolegal->nombre,
                            'abreviatura' => $orden->causa->tipolegal->abreviatura,
                        ] : null,
                    ],

                    // Tribunal
                    'expediente' => optional($orden->causa->tribunales->first())->expediente,

                    // Última foja de todas las descargas de la causa
                    'ultima_foja' => optional(
                        $orden->causa->ordenes
                            ->map->descarga // obtenemos cada descarga (hasOne)
                            ->filter()      // quitamos null (órdenes sin descarga)
                            ->sortByDesc('id')
                            ->first()
                    )->ultima_foja,
                ];
            });
    }
    public function sumatoriaGastoPorCausaYFecha($causaId, $fechaCierre)
    {
        $fechaStr = urldecode($fechaCierre);
        $hasta = Carbon::parse($fechaStr)->endOfDay();
        $ordenes = Orden::with('finalCostos')
            ->where('causa_id', $causaId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('etapa_orden', EtapaOrden::CERRADA)
            ->where('fecha_cierre', '<=', $hasta)
            ->get();

        return $ordenes->sum(function ($orden) {
            return ($orden->finalCostos->total_egreso ?? 0) + ($orden->propina ?? 0);
        });
    }
    public function obtenerOrdenesDetalleFinancieroDeCausa($causaId)
    {
        return Orden::query()
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('causa_id', '=', $causaId)
            // Ordenadas por id ascendente
            ->orderBy('id', 'asc')

            // Eager load relaciones necesarias
            ->with([
                'presupuesto:id,orden_id,detalle_presupuesto',
                'cotizacion:id,orden_id,condicion,venta',
                'descarga:id,orden_id,detalle_informacion,compra_judicial',
                'finalCostos:id,orden_id,costo_procesal_venta,total_egreso,es_validado'
            ])
            ->get()
            ->map(function ($orden) {
                return [
                    'id'                  => $orden->id,
                    'entrega_informacion'        => $orden->entrega_informacion,
                    'detalle_informacion' => optional($orden->descarga)->detalle_informacion,
                    'fecha_fin'           => $orden->fecha_fin,
                    'prioridad'        => $orden->prioridad,
                    'condicion'        => optional($orden->cotizacion)->condicion,
                    'compra_judicial' => optional($orden->descarga)->compra_judicial,
                    'costo_procesal_venta' => optional($orden->finalCostos)->costo_procesal_venta,
                    'venta' => optional($orden->cotizacion)->venta,
                    'costo_procuraduria_venta' => optional($orden->finalCostos)->costo_procuraduria_venta,
                    'total_egreso' => optional($orden->finalCostos)->total_egreso,
                    'etapa_orden' => $orden->etapa_orden,
                    'es_validado' => optional($orden->finalCostos)->es_validado,
                ];
            });
    }
}

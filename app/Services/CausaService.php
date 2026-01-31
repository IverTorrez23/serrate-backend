<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\EstadoCausa;
use App\Constants\EtapaOrden;
use App\Constants\TipoUsuario;
use App\Models\Causa;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\BilleteraService;

class CausaService
{
    protected $billeteraService;
    protected $parametroVigenciaService;

    public function __construct(BilleteraService $billeteraService, ParametroVigenciaService $parametroVigenciaService)
    {
        $this->billeteraService = $billeteraService;
        $this->parametroVigenciaService = $parametroVigenciaService;
    }
    public function store($data)
    {
        $causa = Causa::create([
            'nombre' => $data['nombre'],
            'observacion' => $data['observacion'],
            'objetivos' => $data['objetivos'],
            'estrategia' => $data['estrategia'],
            'informacion' => $data['informacion'],
            'apuntes_juridicos' => $data['apuntes_juridicos'],
            'apuntes_honorarios' => $data['apuntes_honorarios'],
            'tiene_billetera' => $data['tiene_billetera'],
            'billetera' => $data['billetera'],
            'saldo_devuelto' => $data['saldo_devuelto'],
            'color' => $data['color'],
            'materia_id' => $data['materia_id'],
            'tipolegal_id' => $data['tipolegal_id'],
            'categoria_id' => $data['categoria_id'],
            'abogado_id' => $data['abogado_id'],
            'procurador_id' => $data['procurador_id'],
            'usuario_id' => $data['usuario_id'],
            'plantilla_id' => $data['plantilla_id'],

            'estado' => EstadoCausa::ACTIVA,
            'motivo_congelada' => $data['motivo_congelada'],
            'es_eliminado' => 0
        ]);
        return $causa;
    }
    public function update($data, $causaId)
    {
        $causa = Causa::findOrFail($causaId);
        $causa->update($data);
        return $causa;
    }
    public function obtenerUno($causaId)
    {
        $causa = Causa::findOrFail($causaId);
        $causa->load('materia');
        $causa->load('tipoLegal');
        $causa->load('categoria');
        $causa->load('abogado.persona');
        $causa->load('procurador.persona');
        return $causa;
    }
    public function listarActivos()
    {
        $causas = Causa::where('estado', EstadoCausa::ACTIVA)
            ->where('es_eliminado', 0)
            ->get();
        $causas->load('materia');
        $causas->load('tipoLegal');
        $causas->load('categoria');
        $causas->load('abogado.persona');
        $causas->load('procurador.persona');
        return $causas;
    }
    public function listarCausasParaPaquete()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('estado', EstadoCausa::CONGELADA)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->with([
                'materia',
                'tipoLegal'
            ])
            ->whereDoesntHave('paqueteCausas', function ($query) {
                // Verificar que no tengan registros activos en paquete_causas
                $query->where('estado', Estado::ACTIVO)
                    ->where('es_eliminado', 0);
            })
            ->get();
        return $causas;
    }
    public function tieneOrdenesNoCerradas($causaId): bool
    {
        return Orden::where('causa_id', $causaId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->exists();
    }
    public function cuasaNoEstaActiva($causaId): bool
    {
        return Causa::where('id', $causaId)
            ->whereIn('estado', [EstadoCausa::CONGELADA, EstadoCausa::TERMINADA, EstadoCausa::BLOQUEADA])
            ->where('es_eliminado', 0)
            ->exists();
    }
    public function listarCausasConBilletera()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('es_eliminado', 0)
            ->where('tiene_billetera', 1)
            ->where('usuario_id', $usuarioId)
            ->with([
                'materia',
                'tipoLegal'
            ])
            ->get();
        return $causas;
    }
    public function listarCausasDestinoTransaccion()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('es_eliminado', 0)
            ->where('tiene_billetera', 1)
            ->where('usuario_id', $usuarioId)
            ->with([
                'materia',
                'tipoLegal'
            ])
            ->get();
        return $causas;
    }
    public function listadoDetallesCausasConBilleterasDeUsuario()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('es_eliminado', 0)
            ->where('tiene_billetera', 1)
            ->where('usuario_id', $usuarioId)
            ->with(['materia', 'tipoLegal', 'primerDemandante', 'primerDemandado', 'primerTribunal'])
            ->get();
        return $causas;
    }
    public function obtenerCodigoIdentificadorVisual($causaId): ?string
    {
        $causa = Causa::with(['materia', 'tipoLegal'])->find($causaId);

        if (!$causa || !$causa->materia || !$causa->tipoLegal) {
            return null;
        }
        $codigo = $causa->materia->abreviatura . '-' . $causa->tipoLegal->abreviatura . '-' . $causa->id;
        return $codigo;
    }
    public function obtenerDineroComprometidoCausa(int $causaId): float
    {
        $causa = Causa::findOrFail($causaId);

        return $causa->getTotalDineroComprometidoOrdenesDeCausa();
    }
    public function obtenerTotalComprometidoSinBilletera($usuarioId): float
    {
        $total = 0;
        // Obtener causas sin billetera
        $causas = Causa::where('tiene_billetera', 0)
            ->where('usuario_id', $usuarioId)
            ->where('es_eliminado', 0)->with([
                'ordenes' => function ($query) {
                    $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0);
                },
                'ordenes.cotizacion',
                'ordenes.presupuesto',
                'ordenes.descarga',
            ])->get();

        foreach ($causas as $causa) {
            foreach ($causa->ordenes as $orden) {
                $venta = $orden->cotizacion->venta ?? 0;
                $monto = $orden->presupuesto->monto ?? 0;
                $saldoDescarga = $orden->descarga->saldo ?? 0;
                $saldoDescargaFormateado = $saldoDescarga !== 0 ? $saldoDescarga * -1 : 0;
                $propinaPrometida = $orden->propina ?? 0;
                $total += $venta + $monto + $saldoDescargaFormateado + $propinaPrometida;
            }
        }

        return $total;
    }

    public function noPasoValidacionEAPECausa($causaId, $montoProbable): bool
    {
        $causa = Causa::findOrFail($causaId);
        $idUserCausa = $causa->usuario_id;
        $montoTotalProbableComprometido = 0;
        $saldoTotal = 0;
        //Si la causa tiene billetera individual, se hace un calculo de una causa
        if ($causa->tiene_billetera === 1) {
            $montoComprometido = $this->obtenerDineroComprometidoCausa($causaId);
            $montoTotalProbableComprometido = $montoComprometido + $montoProbable;
            $saldoTotal = $causa->billetera;
        } else { //si no tiene billetera, se hace un calculo de las causas sin billeteras y billetera  del usuario
            $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($idUserCausa);
            $montoComprometido = $this->obtenerTotalComprometidoSinBilletera($idUserCausa);
            $montoTotalProbableComprometido = $montoComprometido + $montoProbable;
            $saldoTotal = $billetera->monto;
        }
        //Bloqueo de Causa cuando no pasa EAP
        /*if ($montoTotalProbableComprometido > $saldoTotal) {
            if ($causa->estado === EstadoCausa::ACTIVA) {
                $motivoBloqueo = 'FALTA DE SALDO';
                $this->bloquearCausa($causaId, $motivoBloqueo);
            }
        }*/
        return $montoTotalProbableComprometido > $saldoTotal;
    }
    //Funcion eape cuando se hace una transaccion directamente desde la billetera general, (no hay causa de por medio)
    public function noPasoValidacionEAPEBilleteraGral($montoProbable): bool
    {
        $usuarioId = Auth::user()->id;
        //se hace un calculo de las causas sin billeteras y billetera  del usuario
        $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($usuarioId);
        $montoComprometido = $this->obtenerTotalComprometidoSinBilletera($usuarioId);
        $montoTotalProbableComprometido = $montoComprometido + $montoProbable;
        $saldoTotal = $billetera->monto;

        return $montoTotalProbableComprometido > $saldoTotal;
    }
    public function usuarioTieneCausasNoTerminadas($usuarioId): bool
    {
        return Causa::where('usuario_id', $usuarioId)
            ->where('es_eliminado', 0)
            ->where('estado', '!=', EstadoCausa::TERMINADA)
            ->exists();
    }
    public function usuarioTieneCausasConSaldo($usuarioId): bool
    {
        return Causa::where('usuario_id', $usuarioId)
            ->where('tiene_billetera', 1)
            ->where('es_eliminado', 0)
            ->where('billetera', '>', 0)
            ->exists();
    }
    public function bloquearCausa($causaId, $motivoBloqueo)
    {
        $causa = Causa::findOrFail($causaId);
        $causa->estado = EstadoCausa::BLOQUEADA;
        $causa->motivo_congelada = $motivoBloqueo;
        $causa->save();
        return $causa;
    }
    public function activarCausa($causaId)
    {
        $causa = Causa::findOrFail($causaId);
        if ($this->parametroVigenciaService->hayPaqueteVigente($causa->usuario_id)) {
            $causa->estado = EstadoCausa::ACTIVA;
            $causa->motivo_congelada = '';
            $causa->save();
        }

        return $causa;
    }
    public function abogadoTienePermisoCausa($causaId): bool
    {
        $causa = Causa::findOrFail($causaId);
        $tipoUsuario = Auth::user()->tipo;
        $idUsuario = Auth::user()->id;

        if ($tipoUsuario === TipoUsuario::ABOGADO_INDEPENDIENTE || $tipoUsuario === TipoUsuario::ABOGADO_LIDER) {
            return $idUsuario === $causa->usuario_id;
        }

        if ($tipoUsuario === TipoUsuario::ABOGADO_DEPENDIENTE) {
            return $idUsuario === $causa->abogado_id;
        }

        return false;
    }
    public function obtenerUnaCausa($causaId)
    {
        $causa = Causa::findOrFail($causaId);
        return $causa;
    }
    public function actualizarEstadoPorUsuario($usuarioId)
    {
        return Causa::where('usuario_id', $usuarioId)
            ->where('estado', EstadoCausa::ACTIVA)
            ->where('es_eliminado', 0)
            ->update([
                'estado' => EstadoCausa::CONGELADA,
                'motivo_congelada' => 'FALTA DE PAQUETE'
            ]);
    }
    public function activarEstadoPorUsuario($usuarioId)
    {
        return Causa::where('usuario_id', $usuarioId)
            ->where('estado', EstadoCausa::CONGELADA)
            ->where('es_eliminado', 0)
            ->update([
                'estado' => EstadoCausa::ACTIVA,
                'motivo_congelada' => ''
            ]);
    }
    public function listadoCausasActivasConBilleteras()
    {
        $causas = Causa::where('es_eliminado', 0)
            ->where('estado', EstadoCausa::ACTIVA)
            ->where('tiene_billetera', 1)
            ->with(['materia', 'tipoLegal', 'usuario.persona'])
            ->get();
        return $causas;
    }
    public function listadoCausasTerminadasConBilleteras()
    {
        $causas = Causa::where('es_eliminado', 0)
            ->where('estado', EstadoCausa::TERMINADA)
            ->where('tiene_billetera', 1)
            ->with(['materia', 'tipoLegal', 'usuario.persona'])
            ->get();
        return $causas;
    }
}

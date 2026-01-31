<?php

namespace App\Models;

use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Constants\TipoParticipante;
use App\Traits\CommonsScopesCausa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Causa extends Model
{
    use CommonsScopesCausa, HasFactory;
    protected $fillable = [
        'nombre',
        'observacion',
        'objetivos',
        'estrategia',
        'informacion',
        'apuntes_juridicos',
        'apuntes_honorarios',
        'tiene_billetera',
        'billetera',
        'saldo_devuelto',
        'color',
        'materia_id',
        'tipolegal_id',
        'categoria_id',
        'abogado_id',
        'procurador_id',
        'usuario_id',
        'plantilla_id',
        'estado',
        'motivo_congelada',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Causa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function tipoLegal()
    {
        return $this->belongsTo(TipoLegal::class, 'tipolegal_id');
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function abogado()
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }
    public function procurador()
    {
        return $this->belongsTo(User::class, 'procurador_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    /**
     * Get all of the comments for the Causa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tribunales()
    {
        return $this->hasMany(Tribunal::class, 'causa_id');
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'causa_id');
    }
    public function depositos()
    {
        return $this->hasMany(Deposito::class, 'causa_id');
    }
    public function devolucionesSaldo()
    {
        return $this->hasMany(DevolucionSaldo::class, 'causa_id');
    }
    public function agendaApuntes()
    {
        return $this->hasMany(AgendaApunte::class, 'causa_id');
    }
    public function causaPostas()
    {
        return $this->hasMany(CausaPosta::class, 'causa_id');
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'causa_id');
    }
    public function ordenesUrgencias()
    {
        return $this->hasMany(Orden::class, 'causa_id')
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->whereDoesntHave('descarga'); // Aquí filtramos que no tenga descargas
    }

    public function paqueteCausas()
    {
        return $this->hasMany(PaqueteCausa::class, 'causa_id');
    }
    public function TransaccionesCausas()
    {
        return $this->hasMany(TransaccionesCausa::class, 'causa_id');
    }
    public function ultimaDescarga()
    {
        return $this->hasManyThrough(ProcuraduriaDescarga::class, Orden::class)
            ->latest('created_at') // Ordena por la columna de fecha de creación
            ->first(); // Obtiene solo el primer (último) registro
    }

    public function primerDemandante()
    {
        return $this->hasOne(Participante::class)
            ->where('tipo', TipoParticipante::DEMANDANTE)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->orderBy('id');
    }
    public function primerDemandado()
    {
        return $this->hasOne(Participante::class)
            ->where('tipo', TipoParticipante::DEMANDADO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->orderBy('id');
    }
    public function primerTribunal()
    {
        return $this->hasOne(Tribunal::class)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->with([
                'claseTribunal',
                'juzgado.distrito',
                'juzgado.piso',
                'cuerpoExpedientes'
            ])
            ->orderBy('id');
    }
    public function getTotalDineroComprometidoOrdenesDeCausa(): float
    {
        $ordenesAbiertas = $this->ordenes()->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)->get();

        $total = 0;

        foreach ($ordenesAbiertas as $orden) {
            $venta = $orden->cotizacion->venta ?? 0;
            $monto = $orden->presupuesto->monto ?? 0;
            $saldoDescarga = $orden->descarga->saldo ?? 0;
            $saldoDescargaFormateado = $saldoDescarga !== 0 ? $saldoDescarga * -1 : 0;
            $propinaPrometida = $orden->propina ?? 0;
            $total += $venta + $monto + $saldoDescargaFormateado + $propinaPrometida;
        }

        return $total;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcuraduriaDescarga extends Model
{
    use HasFactory;
    protected $fillable=[
        'detalle_informacion',
        'detalle_documentacion',
        'ultima_foja',
        'gastos',
        'saldo',
        'detalle_gasto',
        'fecha_descarga',
        'compra_judicial',
        'es_validado',
        'orden_id',
        'estado',
        'es_eliminado'
    ];
    /**
     * Get the user that owns the ProcuraduriaDescarga
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
    /**
     * Get the user associated with the ProcuraduriaDescarga
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function confirmacion()
    {
        return $this->hasOne(Confirmacion::class, 'descarga_id', 'id');
    }


}

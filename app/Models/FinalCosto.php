<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalCosto extends Model
{
    use HasFactory;
    protected $fillable=[
        'costo_procuraduria_compra',
        'costo_procuraduria_venta',
        'costo_procesal_compra',
        'costo_procesal_venta',
        'total_egreso',
        'penalidad',
        'es_validado',
        'cancelado_procurador',
        'ganancia_procuraduria',
        'ganancia_procesal',
        'orden_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the orden that owns the FinalCosto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    use HasFactory;
    protected $fillable=[
        'monto',
        'detalle_presupuesto',
        'fecha_presupuesto',
        'fecha_entrega',
        'contador_id',
        'orden_id',
        'estado',
        'es_eliminado'
    ];
    /**
     * Get the user that owns the Presupuesto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
}

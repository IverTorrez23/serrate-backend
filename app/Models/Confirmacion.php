<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmacion extends Model
{
    use HasFactory;
    protected $fillable=[
        'confir_sistema',
        'confir_abogado',
        'fecha_confir_abogado',
        'confir_contador',
        'fecha_confir_contador',
        'justificacion_rechazo',
        'descarga_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Confirmacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function descarga()
    {
        return $this->belongsTo(ProcuraduriaDescarga::class, 'descarga_id');
    }
}

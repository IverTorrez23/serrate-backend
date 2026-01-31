<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaccionesCausa extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'monto',
        'fecha_transaccion',
        'tipo',
        'transaccion',
        'glosa',
        'causa_id',
        'causa_origen_destino',
        'orden_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the TransaccionesCausa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
}

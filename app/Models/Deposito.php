<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha_deposito',
        'detalle_deposito',
        'monto',
        'tipo',
        'causa_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Deposito
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaApunte extends Model
{
    use HasFactory;
    protected $fillable=[
        'detalle_apunte',
        'fecha_inicio',
        'fecha_fin',
        'color',
        'causa_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the causa that owns the AgendaApunte
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionAlternativa extends Model
{
    use HasFactory;
    protected $fillable=[
        'solicitud_gestion',
        'fecha_solicitud',
        'tribunal_id',
        'cuerpo_expediente_id',
        'detalle_gestion',
        'fecha_respuesta',
        'orden_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the orden that owns the GestionAlternativa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
    public function tribunal()
    {
        return $this->belongsTo(Tribunal::class, 'tribunal_id');
    }
    public function cuerpoExpediente()
    {
        return $this->belongsTo(CuerpoExpediente::class, 'cuerpo_expediente_id');
    }

    public function registroLlamadas()
    {
        return $this->hasMany(RegistroLlamada::class, 'gestion_id');
    }
}

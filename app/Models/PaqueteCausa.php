<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaqueteCausa extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'compra_paquete_id',
        'causa_id',
        'fecha_asociacion',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the PaqueteCausa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function compraPaquete()
    {
        return $this->belongsTo(CompraPaquete::class, 'compra_paquete_id');
    }
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
}

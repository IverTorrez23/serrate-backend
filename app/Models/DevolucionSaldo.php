<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionSaldo extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'fecha_devolucion',
        'glosa',
        'monto',
        'billetera_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the causa that owns the DevolucionSaldo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function billetera()
    {
        return $this->belongsTo(Billetera::class, 'billetera_id');
    }
}

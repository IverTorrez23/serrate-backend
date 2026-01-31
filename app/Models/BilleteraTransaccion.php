<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BilleteraTransaccion extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'monto',
        'fecha_transaccion',
        'tipo',
        'glosa',
        'billetera_id',
        'orden_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
    /**
     * Get the user that owns the BilleteraTransaccion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function billetera()
    {
        return $this->belongsTo(Billetera::class, 'billetera_id');
    }
}

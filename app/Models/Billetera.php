<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billetera extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'monto',
        'abogado_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Billetera
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function abogado()
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }
    /**
     * Get all of the comments for the Billetera
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billeteraTransacciones()
    {
        return $this->hasMany(BilleteraTransaccion::class, 'billetera_id', 'id');
    }
}

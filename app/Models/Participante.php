<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'nombres',
        'tipo',
        'foja',
        'ultimo_domicilio',
        'causa_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the causa that owns the Participante
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
}

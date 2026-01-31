<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcuradorPago extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'monto',
        'tipo',
        'fecha_pago',
        'fecha_inicio_consulta',
        'fecha_fin_consulta',
        'glosa',
        'procurador_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];

    public function procurador()
    {
        return $this->belongsTo(User::class, 'procurador_id');
    }
}

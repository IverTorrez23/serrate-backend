<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroVigencia extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha_ultima_vigencia',
        'usuario_id',
        'esta_vigente',
        'estado',
        'es_eliminado'
    ];

    public function abogado()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

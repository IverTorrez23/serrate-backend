<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retiro extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'monto',
        'fecha_retiro',
        'glosa',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

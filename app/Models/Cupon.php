<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'codigo',
        'fecha_uso',
        'paquete_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'paquete_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

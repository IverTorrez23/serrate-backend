<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'link',
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'es_eliminado'
    ];
}

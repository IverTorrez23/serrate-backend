<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformePosta extends Model
{
    use HasFactory;
    protected $fillable=[
        'foja_informe',
        'fecha_informe',
        'calculo_gasto',
        'honorario_informe',
        'foja_truncamiento',
        'fecha_truncamiento',
        'honorario_informe_truncamiento',
        'esta_escrito',
        'tipoposta_id',
        'causaposta_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the tipoPosta that owns the InformePosta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoPosta()
    {
        return $this->belongsTo(TipoPosta::class, 'tipoposta_id');
    }
    public function causaPosta()
    {
        return $this->belongsTo(CausaPosta::class, 'causaposta_id');
    }
}

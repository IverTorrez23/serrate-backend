<?php
namespace App\Constants;

class TipoUsuario
{
    const ADMINISTRADOR = 'ADMINISTRADOR';
    const ABOGADO_INDEPENDIENTE = 'ABOGADO_INDEPENDIENTE';
    const ABOGADO_LIDER = 'ABOGADO_LIDER';
    const ABOGADO_DEPENDIENTE = 'ABOGADO_DEPENDIENTE';
    const PROCURADOR = 'PROCURADOR';
    const CONTADOR = 'CONTADOR';
    const PROCURADOR_MAESTRO = 'PROCURADOR_MAESTRO';
    const OBSERVADOR = 'OBSERVADOR';

    public static function getValues()
    {
        return [
            self::ADMINISTRADOR,
            self::ABOGADO_INDEPENDIENTE,
            self::ABOGADO_LIDER,
            self::ABOGADO_DEPENDIENTE,
            self::PROCURADOR,
            self::CONTADOR,
            self::PROCURADOR_MAESTRO,
            self::OBSERVADOR,
        ];
    }
}


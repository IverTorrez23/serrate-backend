<?php

namespace App\Constants;

use Carbon\Carbon;

class FechaHelper
{
    public const ZONA_HORARIA_BOLIVIA = 'America/La_Paz';

    public static function ahoraBolivia(): Carbon
    {
        return Carbon::now(self::ZONA_HORARIA_BOLIVIA);
    }

    public static function fechaHoraBolivia(): string
    {
        return self::ahoraBolivia()->toDateTimeString();
    }

    public static function soloFechaBolivia(): string
    {
        return self::ahoraBolivia()->toDateString();
    }
}

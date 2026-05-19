<?php
namespace App\Constants;

//funcion que selecciona automaticamente la ruta segun si es prod o local
class GlobalVar
{
    public static function path($subPath = '')
    {
        $base = app()->environment('production')
            ? PathEnv::BASE_PATH_PROD
            : PathEnv::BASE_PATH_DEV;

        return $base . ltrim($subPath, '/');
    }
}

class PathEnv
{
    const BASE_PATH_DEV = '';
    const BASE_PATH_PROD = '/home/sites/htyg9449/public_html/api.teleprocuraduria.lex.net.bo/';
}
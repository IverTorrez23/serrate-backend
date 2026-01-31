<?php

namespace App\Enums;

enum MessageHttp: string
{
    case OBTENIDO_CORRECTAMENTE = 'Registro obtenido correctamente.';
    case OBTENIDOS_CORRECTAMENTE = 'Registros obtenidos correctamente.';
    case ERROR_OBTENER_DATOS = 'Error al obtener datos.';
    case CREADO_CORRECTAMENTE = 'Registro creado correctamente.';
    case ACTUALIZADO_CORRECTAMENTE = 'Registro actualizado correctamente.';
    case ELIMINADO_CORRECTAMENTE = 'Registro eliminado correctamente.';
    case ERROR_CREAR = 'Error al crear el registro.';
    case ERROR_ACTUALIZAR = 'Error al actualizar el registro.';
    case ERROR_ELIMINAR = 'Error al eliminar el registro.';
}

<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnvioCorreoNativoService
{
    public function enviar(string $para, string $asunto, string $mensaje, string $deNombre = 'Teleprocuraduria', string $deCorreo = 'no-reply@teleprocuraduria.lex.net.bo')
    {
        // Definir los encabezados (Headers) para que el correo no llegue como SPAM y soporte HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $deNombre . " <" . $deCorreo . ">" . "\r\n";
        $headers .= "Reply-To: " . $deCorreo . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Intentar enviar el correo
        try {
            return mail($para, $asunto, $mensaje, $headers);
        } catch (\Exception $e) {
            // Aquí podrías loguear el error si algo falla
            Log::error("Error enviando correo nativo: " . $e->getMessage());
            return false;
        }
    }
    
    
}

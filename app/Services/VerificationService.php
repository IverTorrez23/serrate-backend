<?php

namespace App\Services;

use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\JsonResponse;

class VerificationService
{
    protected $envioCorreoNativoService;
    public function __construct(EnvioCorreoNativoService $envioCorreoNativoService)
    {
        $this->envioCorreoNativoService = $envioCorreoNativoService;
    }

    public function sendVerificationCode($email): JsonResponse
    {
        if ($this->isRateLimited($email)) {
            return ResponseService::error(
                'Demasiados intentos. Intente más tarde.',
                429
            );
        }

        $existingCode = VerificationCode::where('email', $email)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingCode) {
            return ResponseService::success(
                message: 'Ya existe un código de verificación activo.',
                //data: $existingCode
            );
        }

        $verificationCode = strtoupper(Str::random(6));
        $expiresAt = Carbon::now()->addMinutes(config('verification_code.expiration_time', 15));

        $verification = VerificationCode::create([
            'email' => $email,
            'verification_code' => $verificationCode,
            'expires_at' => $expiresAt,
            'used' => false,
        ]);

        try {
            // Enviar el código por correo Mailtrap
            //Mail::to($email)->send(new VerificationCodeMail($verificationCode));
            $asunto = 'Código de verificación';
            $resultado = $this->procesarEnvioCorreo($email, $asunto, $verificationCode);
            return ResponseService::success(
                message: 'Correo de verificación enviado.',
                //data: $verification
            );
        } catch (\Exception $e) {

            return ResponseService::error(
                'No se pudo enviar el correo de verificación. Inténtalo de nuevo más tarde.',
                500
            );
        }
    }

    public function verifyCode($email, $verificationCode): JsonResponse
    {
        $verification = VerificationCode::where('email', $email)
            ->where('verification_code', $verificationCode)
            ->first();


        if (!$verification) {
            return ResponseService::error(
                'Código de verificación inválido.',
                400
            );
        }

        if ($verification->used) {
            return ResponseService::error(
                'El código de verificación ya ha sido utilizado.',
                400
            );
        }

        if (now()->greaterThan($verification->expires_at)) {
            return ResponseService::error(
                'El código ha expirado.',
                400
            );
        }

        $verification->used = true;
        $verification->save();

        return ResponseService::success(
            message: 'Correo verificado con éxito.'
        );
    }

    private function isRateLimited($email): bool
    {
        $key = 'send-verification-code:' . $email;

        if (RateLimiter::tooManyAttempts($key, config('verification_code.rate_limit', 5))) {
            return true;
        }

        RateLimiter::hit($key, config('verification_code.decay_minutes', 10) * 60);

        return false;
    }
    public function procesarEnvioCorreo($emailDestino, $asunto, $codigo)
    {
        $cuerpo = '<!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Código de Verificación</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        color: #333333;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 20px auto;
                        background: #ffffff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        background: #4CAF50;
                        color: #ffffff;
                        text-align: center;
                        padding: 20px;
                        font-size: 24px;
                    }
                    .content {
                        padding: 20px;
                        text-align: center;
                        font-size: 16px;
                    }
                    .code {
                        font-size: 24px;
                        font-weight: bold;
                        color: #4CAF50;
                        background: #f4f4f4;
                        padding: 10px;
                        margin: 20px auto;
                        display: inline-block;
                        border-radius: 5px;
                    }
                    .footer {
                        background: #f4f4f4;
                        color: #777777;
                        text-align: center;
                        padding: 10px;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        Código de Verificación
                    </div>
                    <div class="content">
                        <p>Hola,</p>
                        <p>Gracias por utilizar nuestro servicio. Tu código de verificación es:</p>
                        <div class="code">' . htmlspecialchars($codigo) . '</div>
                        <p>Este código expirará en 15 minutos. Si no solicitaste este código, puedes ignorar este mensaje.</p>
                        <p>¡Gracias!</p>
                    </div>
                    <div class="footer">
                        © ' . date('Y') . ' TELEPROCURADURIA. Todos los derechos reservados.
                    </div>
                </div>
            </body>
            </html>';

        // Usamos la función de nuestro servicio
        $resultado = $this->envioCorreoNativoService->enviar($emailDestino, $asunto, $cuerpo);

        if ($resultado) {
            return true;
        } else {
            return false;
        }
    }
}

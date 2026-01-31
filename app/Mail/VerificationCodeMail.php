<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VerificationCodeMail extends Mailable
{
    public $verificationCode;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function build()
    {
        return $this->subject('Código de verificación')
            ->html($this->buildEmailContent());
    }

    private function buildEmailContent()
    {
        return '
            <!DOCTYPE html>
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
                        <div class="code">' . htmlspecialchars($this->verificationCode) . '</div>
                        <p>Este código expirará en 15 minutos. Si no solicitaste este código, puedes ignorar este mensaje.</p>
                        <p>¡Gracias!</p>
                    </div>
                    <div class="footer">
                        © ' . date('Y') . ' TELEPROCURADURIA. Todos los derechos reservados.
                    </div>
                </div>
            </body>
            </html>
        ';
    }
}

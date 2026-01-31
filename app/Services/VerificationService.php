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
            Mail::to($email)->send(new VerificationCodeMail($verificationCode));
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
}

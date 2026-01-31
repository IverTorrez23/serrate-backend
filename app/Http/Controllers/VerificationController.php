<?php

namespace App\Http\Controllers;

use App\Constants\ErrorMessages;
use App\Http\Requests\SendVerificationCodeRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Services\ResponseService;
use App\Services\VerificationService;
use Exception;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    protected $verificationCodeService;

    public function __construct(VerificationService $verificationCodeService)
    {
        $this->verificationCodeService = $verificationCodeService;
    }

    public function sendVerificationCode(SendVerificationCodeRequest $request): JsonResponse
    {

        try {
            return $this->verificationCodeService->sendVerificationCode($request->email);
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_ENVIAR_EMAIL, 500);
        }
    }


    public function verifyCode(VerifyCodeRequest $request): JsonResponse
    {

        try {
            return $this->verificationCodeService->verifyCode($request->email, $request->verification_code);
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_VALIDACION, 500);
        }
    }
}

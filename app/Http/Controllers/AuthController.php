<?php

namespace App\Http\Controllers;

use App\Constants\ErrorMessages;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function crearUsuario(RegisterRequest $request): JsonResponse
    {
        try {
            return $this->authService->crearUsuario($request->validated());
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_CREAR, 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            return $this->authService->login($request->validated());
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_LOGIN, 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {

            return $this->authService->logout($request);
        } catch (Exception $e) {
            return ResponseService::error('Error inesperado al cerrar sesión.', 500);
        }
    }
}

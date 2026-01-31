<?php

use App\Http\Middleware\LogHttpRequests;
use App\Services\ResponseService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global
        $middleware->append(LogHttpRequests::class);

        // Middleware alias (para rutas)
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'auth:sanctum' => EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Registro de excepciones
        $exceptions->reportable(function (Throwable $e) {
            Log::error($e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTrace(),
            ]);
        });

        // Manejo de excepciones
        $exceptions->renderable(function (ValidationException $e) {
            return ResponseService::validationError($e->errors(), 'Hay errores en los datos enviados.');
        });

        $exceptions->renderable(function (AuthenticationException $e) {
            return ResponseService::unauthorized('No estás autenticado.');
        });

        $exceptions->renderable(function (AuthorizationException $e) {
            return ResponseService::forbidden('Acceso no autorizado.');
        });

        $exceptions->renderable(function (NotFoundHttpException $e) {
            return ResponseService::notFound('Recurso no encontrado');
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e) {
            return ResponseService::error('Método no permitido', 405);
        });

        // Error genérico en el servidor
        $exceptions->renderable(function (Throwable $e) {
            return ResponseService::error('Error interno. Intenta más tarde.', 500);
        });
    })
    ->create();

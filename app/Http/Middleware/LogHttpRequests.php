<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogHttpRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            $executionTime = number_format((microtime(true) - $startTime) * 1000, 2);

            if (app()->environment('local')) {
                $errorData = [
                    'usuario' => [
                        'id' => optional($request->user())->id,
                        'email' => optional($request->user())->email,
                    ],
                    'ip' => $request->ip(),
                    'método' => $request->method(),
                    'url' => $request->fullUrl(),
                    'cabeceras' => [
                        'User-Agent' => $request->header('User-Agent'),
                        'Accept' => $request->header('Accept'),
                    ],
                    'parámetros' => $this->getFilteredParameters($request),
                    'estado_petición' => 'ERROR',
                    'respuesta' => [
                        'código_http' => 500,
                        'mensaje' => $e->getMessage(),
                        'archivo' => $e->getFile(),
                        'línea' => $e->getLine(),
                        'trace' => collect($e->getTrace())->take(3),
                    ],
                    'tiempo_de_ejecución_ms' => $executionTime,
                ];

                Log::error('❌ [ERROR] [' . now() . '] ' . json_encode($errorData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            return response()->json(['message' => 'Error interno del servidor'], 500);
        }

        if (app()->environment('local')) {
            $estadoPeticion = $response->getStatusCode() === 200 ? 'ÉXITO' : 'ERROR';

            $logData = [
                'usuario' => [
                    'id' => optional($request->user())->id,
                    'email' => optional($request->user())->email,
                ],
                'ip' => $request->ip(),
                'método' => $request->method(),
                'url' => $request->fullUrl(),
                'cabeceras' => [
                    'User-Agent' => $request->header('User-Agent'),
                    'Accept' => $request->header('Accept'),
                ],
                'parámetros' => $this->getFilteredParameters($request),
                'estado_petición' => $estadoPeticion,
                'respuesta' => [
                    'código_http' => $response->getStatusCode(),
                    'estado' => $estadoPeticion,
                    'contenido' => $this->getResponseContent($response),
                ],
                'tiempo_de_ejecución_ms' => number_format((microtime(true) - $startTime) * 1000, 2),
            ];

            Log::info('📩 [INFO] [' . now() . '] PETICIÓN RECIBIDA: ' . json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return $response;
    }

    protected function getFilteredParameters(Request $request)
    {
        $parameters = $request->all();
        unset($parameters['password'], $parameters['token']);
        return $parameters;
    }

    // Método para obtener el contenido de la respuesta
    protected function getResponseContent(Response $response)
    {
        try {
            $content = $response->getContent();
            return json_decode($content, true) ?? $content;
        } catch (\Throwable $e) {
            return 'Contenido no disponible';
        }
    }
}

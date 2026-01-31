<?php

namespace App\Services;

use App\Constants\ErrorMessages;
use App\Constants\Estado;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Constants\GeneralMessages;
use App\Constants\SuccessMessages;
use App\Constants\TipoUsuario;
use App\Constants\ValidationMessages;
use App\Http\Resources\Auth\UserResource;
use App\Models\Billetera;
use App\Models\Persona;
use App\Models\ParametroVigencia;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function crearUsuario(array $data): JsonResponse
    {
        try {
            DB::transaction(fn() => $this->createUserWithRelations($data));
            return ResponseService::success(message: SuccessMessages::CREADO_CORRECTAMENTE);
        } catch (\Exception $e) {
            Log::error('Error al crear registro: ' . $e->getMessage());
            return ResponseService::error(ErrorMessages::ERROR_CREAR, 500);
        }
    }
    private function createUserWithRelations(array $data): void
    {
        $abogado_id = (auth()->check() && $data['tipo'] === TipoUsuario::ABOGADO_DEPENDIENTE) ? auth()->id() : 0;

        $user = User::create([
            'name' => $data['nombre'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipo' => $data['tipo'],
            'abogado_id' => $abogado_id,
            'opciones_moto' => isset($data['opciones_moto']) ? json_encode($data['opciones_moto']) : null,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => false,
        ]);

        $this->createPersona($data, $user);
        $this->createBilletera($data, $user);
        $this->createParametroVigencia($data, $user);
    }

    private function createPersona(array $data, User $user): void
    {
        Persona::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'telefono' => $data['telefono'],
            'direccion' => $data['direccion'] ?? null,
            'coordenadas' => $data['coordenadas'] ?? null,
            'observacion' => $data['observacion'] ?? null,
            'foto_url' => $data['foto_url'] ?? null,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => false,
            'usuario_id' => $user->id,
        ]);
    }

    private function createBilletera(array $data, User $user): void
    {
        try {
            if (!in_array($data['tipo'], [TipoUsuario::ABOGADO_INDEPENDIENTE, TipoUsuario::ABOGADO_LIDER])) {
                return;
            }

            Billetera::create([
                'monto'        => 0,
                'abogado_id'   => $user->id,
                'estado'       => Estado::ACTIVO,
                'es_eliminado' => false,
            ]);

            Log::info('Billetera created for user: ' . $user->id);
        } catch (\Exception $e) {
            Log::error('Error in createBilletera: ' . $e->getMessage());
            throw $e;
        }
    }
    private function createParametroVigencia(array $data, User $user): void
    {
        try {
            if (!in_array($data['tipo'], [TipoUsuario::ABOGADO_INDEPENDIENTE, TipoUsuario::ABOGADO_LIDER])) {
                return;
            }

            ParametroVigencia::create([
                'fecha_ultima_vigencia' => null,
                'usuario_id'   => $user->id,
                'esta_vigente'   => 0,
                'estado'       => Estado::ACTIVO,
                'es_eliminado' => 0,
            ]);

            Log::info('Parametro created for user: ' . $user->id);
        } catch (\Exception $e) {
            Log::error('Error in createParametroVigencia: ' . $e->getMessage());
            throw $e;
        }
    }

    public function login(array $credentials): JsonResponse
    {
        try {
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                Log::warning('Usuario no encontrado', ['email' => $credentials['email']]);
                return ResponseService::validationError(
                    ['email' => [ValidationMessages::ERROR_VALIDACION_EMAIL]],
                    ErrorMessages::ERROR_AUTENTICACION
                );
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                Log::warning('Contraseña incorrecta', ['email' => $credentials['email']]);
                return ResponseService::validationError(
                    ['password' => [ValidationMessages::ERROR_VALIDACION_PASSWORD]],
                    ErrorMessages::ERROR_AUTENTICACION
                );
            }

            $user->load(['persona']);

            return ResponseService::success([
                'user' => new UserResource($user),
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => now('America/La_Paz')->addMinutes(60)->format('Y-m-d H:i:s'),
            ], GeneralMessages::INICIO_SESION_EXITOSO);
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_LOGIN, 500);
        }
    }


    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ResponseService::unauthorized('Usuario no autenticado.');
            }
            $user->currentAccessToken()->delete();

            return ResponseService::success(message: GeneralMessages::CIERRE_SESION_EXITOSO);
        } catch (Exception $e) {
            return ResponseService::error('Error inesperado al cerrar sesión.', 500);
        }
    }
}

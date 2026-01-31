<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Constants\SuccessMessages;
use App\Constants\ErrorMessages;

class PerfilUsuarioService
{
    public function obtenerPerfil(): JsonResponse
    {
        $user = User::with('persona')->find(Auth::id());

        if (!$user) {
            return ResponseService::unauthorized(ErrorMessages::ERROR_OBTENER_USUARIO);
        }

        return ResponseService::success(
            [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'tipo' => $user->tipo,
                    'persona' => $user->persona ? [
                        'nombre' => $user->persona->nombre,
                        'apellido' => $user->persona->apellido,
                        'telefono' => $user->persona->telefono,
                        'direccion' => $user->persona->direccion,
                        'coordenadas' => $user->persona->coordenadas,
                        'observacion' => $user->persona->observacion,
                        'foto_url' => $user->persona->foto_url,
                    ] : null,
                ]
            ],
            SuccessMessages::OBTENIDO_CORRECTAMENTE,
            200
        );
    }

    public function actualizarPerfil(array $data): JsonResponse
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return ResponseService::unauthorized(ErrorMessages::ERROR_OBTENER_USUARIO);
        }

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $data['persona']['nombre'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'tipo' => $data['tipo'] ?? $user->tipo
            ]);

            if (isset($data['persona']) && $user->persona) {
                $user->persona->update($data['persona']);
            }

            DB::commit();

            return ResponseService::success(
                $user->load('persona'),
                SuccessMessages::ACTUALIZADO_CORRECTAMENTE,
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseService::error(ErrorMessages::ERROR_ACTUALIZAR . ' ' . $e->getMessage(), 500);
        }
    }

    public function cambiarPassword(array $data): JsonResponse
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return ResponseService::unauthorized(ErrorMessages::ERROR_OBTENER_USUARIO);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return ResponseService::success(
            '',
            SuccessMessages::CONTRASENA_ACTUALIZADA_CORRECTAMENTE,
            200
        );
    }

    public function actualizarFotoPerfil($foto): JsonResponse
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return ResponseService::unauthorized(ErrorMessages::ERROR_OBTENER_USUARIO);
        }

        if (!$foto || !$foto->isValid()) {
            return ResponseService::error(ErrorMessages::ERROR_CARGAR_IMAGEN, 400);
        }

        if ($user->persona->foto_url) {
            Storage::disk('public')->delete($user->persona->foto_url);
        }

        $extension = $foto->getClientOriginalExtension();
        $nombreArchivo = Str::slug($user->persona->nombre . '_' . $user->persona->apellido) . '_' . time() . '.' . $extension;

        $rutaFoto = $foto->storeAs('fotos_perfil', $nombreArchivo, 'public');

        if (!$rutaFoto) {
            return ResponseService::error(ErrorMessages::ERROR_CARGAR_IMAGEN, 500);
        }

        $user->persona->update(['foto_url' => $rutaFoto]);

        return ResponseService::success(
            ['foto_url' => $rutaFoto],
            SuccessMessages::FOTO_ACTUALIZADA_CORRECTAMENTE,
            200
        );
    }
}

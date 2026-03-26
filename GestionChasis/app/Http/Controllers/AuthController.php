<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'usuario',
                'activo' => true,
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado exitosamente.',
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'rol' => $user->rol,
                ],
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el usuario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Iniciar sesión.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.',
            ], 401);
        }

        if (!$user->isActive()) {
            return response()->json([
                'message' => 'El usuario no está activo.',
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Sesión iniciada exitosamente.',
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Cerrar sesión.
     */
    public function logout(): JsonResponse
    {
        if (auth()->check()) {
            auth()->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ], 200);
    }

    /**
     * Obtener el usuario autenticado.
     */
    public function me(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        $user = auth()->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
                'activo' => $user->activo,
            ],
        ], 200);
    }

    /**
     * Actualizar perfil del usuario autenticado.
     */
    public function updateProfile(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        $user = auth()->user();
        $data = request()->validate([
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ], [
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Ya existe un usuario con ese correo electrónico.',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Perfil actualizado exitosamente.',
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
            ],
        ], 200);
    }
}

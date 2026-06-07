<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    // POST /api/register — crea usuario y retorna token
    #[OA\Post(
        path: '/api/v1/register',
        tags: ['Autenticación'],
        summary: 'Registrar nuevo usuario',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Juan López'),
                    new OA\Property(property: 'email', type: 'string', example: 'juan@test.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Usuario registrado exitosamente'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function register(Request $request)
    {
        $datos = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $usuario = User::create([
            'name'     => $datos['name'],
            'email'    => $datos['email'],
            'password' => Hash::make($datos['password']),
        ]);

        $token = $usuario->createToken('auth-token')->plainTextToken;

        return response()->json([
            'mensaje' => 'Usuario registrado',
            'token'   => $token,
            'user'    => $usuario,
        ], 201);
    }

    // POST /api/login — verifica email/password y retorna token
    #[OA\Post(
        path: '/api/v1/login',
        tags: ['Autenticación'],
        summary: 'Iniciar sesión',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'admin@tienda.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login exitoso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'token', type: 'string'),
                        new OA\Property(property: 'user', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciales incorrectas'),
        ]
    )]
    public function login(Request $request)
    {
        $datos = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $usuario = User::where('email', $datos['email'])->first();

        if (!$usuario || !Hash::check($datos['password'], $usuario->password)) {
            return response()->json(['mensaje' => 'Credenciales inválidas'], 401);
        }

        $token = $usuario->createToken('auth-token')->plainTextToken;

        return response()->json([
            'mensaje' => 'Login exitoso',
            'token'   => $token,
            'user'    => $usuario,
        ], 200);
    }

    // POST /api/logout — elimina el token actual (requiere estar autenticado)
    #[OA\Post(
        path: '/api/v1/logout',
        tags: ['Autenticación'],
        summary: 'Cerrar sesión',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logout exitoso'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensaje' => 'Logout exitoso'], 200);
    }

    // GET /api/me — retorna el usuario autenticado
    #[OA\Get(
        path: '/api/v1/me',
        tags: ['Autenticación'],
        summary: 'Obtener usuario autenticado con permisos',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Datos del usuario',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'email', type: 'string'),
                        new OA\Property(property: 'rol', type: 'string', enum: ['admin', 'editor', 'cliente']),
                        new OA\Property(property: 'permisos', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'rol'      => $user->rol,
            'permisos' => [
                'crear'    => Gate::forUser($user)->allows('crear-producto'),
                'editar'   => Gate::forUser($user)->allows('editar-producto'),
                'eliminar' => Gate::forUser($user)->allows('eliminar-producto'),
            ],
        ]);
    }
}

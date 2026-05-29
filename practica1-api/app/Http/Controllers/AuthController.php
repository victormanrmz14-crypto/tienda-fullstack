<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // POST /api/register — crea usuario y retorna token
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
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensaje' => 'Logout exitoso'], 200);
    }

    // GET /api/me — retorna el usuario autenticado
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }
}

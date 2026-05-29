<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{producto}', [ProductoController::class, 'show']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::get('/user',    function (Request $request) {
        return $request->user();
    });

    // Solo crear, editar y eliminar requieren autenticación
    Route::post('/productos',             [ProductoController::class, 'store']);
    Route::put('/productos/{producto}',   [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}',[ProductoController::class, 'destroy']);
});

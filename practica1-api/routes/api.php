<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\CategoriaController;

// Ruta de autenticación para canales privados (ANTES de los grupos)
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{producto}', [ProductoController::class, 'show']);
Route::apiResource('categorias', CategoriaController::class)->only(['index', 'show']);
Route::get('categorias/{categoria}/productos', [CategoriaController::class, 'productos']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::get('/user',    function (Request $request) {
        return $request->user();
    });
    Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);

    // Solo crear, editar y eliminar requieren autenticación
    Route::post('/productos',             [ProductoController::class, 'store']);
    Route::put('/productos/{producto}',   [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}',[ProductoController::class, 'destroy']);

    Route::post('pedidos',              [PedidoController::class, 'store']);
    Route::get('pedidos',               [PedidoController::class, 'index']);
    Route::get('pedidos/{pedido}',      [PedidoController::class, 'show']);
});

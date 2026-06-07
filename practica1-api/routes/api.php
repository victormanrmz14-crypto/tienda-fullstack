<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\Api\V2\ProductoController as V2ProductoController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// Broadcasting auth
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// ── Versión 1 (estable) ──────────────────────────────────
Route::prefix('v1')->name('v1.')->group(function () {

    // Rutas públicas v1
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/productos',             [ProductoController::class, 'index']);
    Route::get('/productos/{producto}',  [ProductoController::class, 'show']);
    Route::get('/categorias',            [CategoriaController::class, 'index']);
    Route::get('/categorias/{categoria}',[CategoriaController::class, 'show']);
    Route::get('/categorias/{categoria}/productos', [CategoriaController::class, 'productos']);

    // Rutas protegidas v1
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);

        Route::post('/productos',            [ProductoController::class, 'store']);
        Route::put('/productos/{producto}',  [ProductoController::class, 'update']);
        Route::delete('/productos/{producto}',[ProductoController::class, 'destroy']);

        Route::post('/categorias',             [CategoriaController::class, 'store']);
        Route::put('/categorias/{categoria}',  [CategoriaController::class, 'update']);
        Route::delete('/categorias/{categoria}',[CategoriaController::class, 'destroy']);

        Route::post('/pedidos',          [PedidoController::class, 'store']);
        Route::get('/pedidos',           [PedidoController::class, 'index']);
        Route::get('/pedidos/{pedido}',  [PedidoController::class, 'show']);
    });
});

// ── Versión 2 (nuevas funcionalidades) ──────────────────
Route::prefix('v2')->name('v2.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/productos',            [V2ProductoController::class, 'index']);
        Route::get('/productos/{producto}', [V2ProductoController::class, 'show']);
    });
});

// ── Rutas sin versión (compatibilidad hacia atrás) ───────
// Redirige las rutas viejas a v1 para no romper el frontend actual
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/productos',            [ProductoController::class, 'index']);
Route::get('/productos/{producto}', [ProductoController::class, 'show']);
Route::get('/categorias',           [CategoriaController::class, 'index']);
Route::get('/categorias/{categoria}',[CategoriaController::class, 'show']);
Route::get('/categorias/{categoria}/productos', [CategoriaController::class, 'productos']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/productos',             [ProductoController::class, 'store']);
    Route::put('/productos/{producto}',   [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}',[ProductoController::class, 'destroy']);
    Route::post('/categorias',             [CategoriaController::class, 'store']);
    Route::put('/categorias/{categoria}',  [CategoriaController::class, 'update']);
    Route::delete('/categorias/{categoria}',[CategoriaController::class, 'destroy']);
    Route::post('/pedidos',         [PedidoController::class, 'store']);
    Route::get('/pedidos',          [PedidoController::class, 'index']);
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show']);
});

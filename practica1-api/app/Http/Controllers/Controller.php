<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Mi Tienda Online API',
    description: 'API REST para gestión de tienda construida con Vue.js + Laravel. Incluye autenticación Sanctum, CRUD de productos y categorías, pedidos y roles.',
    contact: new OA\Contact(email: 'admin@tienda.com')
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Servidor de desarrollo local'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'sanctum-token'
)]
abstract class Controller
{
    use AuthorizesRequests;
}

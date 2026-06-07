<?php

namespace App\Http\Controllers;

use App\Events\StockBajoAlerta;
use App\Models\Producto;
use App\Http\Resources\ProductoResource;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class ProductoController extends Controller
{
    #[OA\Get(
        path: '/api/v1/productos',
        tags: ['Productos'],
        summary: 'Listar productos con filtros y paginación',
        parameters: [
            new OA\Parameter(name: 'busqueda', in: 'query', description: 'Buscar por nombre o descripción', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'categoria_id', in: 'query', description: 'Filtrar por categoría', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'precio_min', in: 'query', description: 'Precio mínimo', schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'precio_max', in: 'query', description: 'Precio máximo', schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'page', in: 'query', description: 'Número de página', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista paginada de productos'),
        ]
    )]
    public function index(Request $request)
    {
        $productos = Producto::with('categoria')
            ->buscar($request->busqueda)
            ->deCategoria($request->categoria_id)
            ->rangoPrecio($request->precio_min, $request->precio_max)
            ->orderBy($request->get('orden', 'nombre'), $request->get('dir', 'asc'))
            ->paginate($request->get('por_pagina', 15));

        return ProductoResource::collection($productos);
    }

    #[OA\Post(
        path: '/api/v1/productos',
        tags: ['Productos'],
        summary: 'Crear nuevo producto',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['nombre', 'precio', 'stock'],
                    properties: [
                        new OA\Property(property: 'nombre', type: 'string', example: 'Laptop Dell'),
                        new OA\Property(property: 'descripcion', type: 'string', example: 'Laptop de alto rendimiento'),
                        new OA\Property(property: 'precio', type: 'number', example: 1299.99),
                        new OA\Property(property: 'stock', type: 'integer', example: 10),
                        new OA\Property(property: 'categoria_id', type: 'integer', example: 1),
                        new OA\Property(property: 'imagen', type: 'string', format: 'binary'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Producto creado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreProductoRequest $request)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create($datos);
        $producto->load('categoria');

        return response()->json([
            ...$producto->toArray(),
            'imagen_url' => $producto->imagen
                ? asset('storage/' . $producto->imagen)
                : null,
        ], 201);
    }

    #[OA\Get(
        path: '/api/v1/productos/{id}',
        tags: ['Productos'],
        summary: 'Obtener un producto por ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Datos del producto'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function show(Producto $producto)
    {
        return response()->json([
            ...$producto->toArray(),
            'imagen_url' => $producto->imagen
                ? asset('storage/' . $producto->imagen)
                : null,
        ], 200);
    }

    #[OA\Put(
        path: '/api/v1/productos/{id}',
        tags: ['Productos'],
        summary: 'Actualizar producto existente',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nombre', type: 'string'),
                    new OA\Property(property: 'precio', type: 'number'),
                    new OA\Property(property: 'stock', type: 'integer'),
                    new OA\Property(property: 'categoria_id', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Producto actualizado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($datos);
        $producto->load('categoria');

        $producto->refresh();
        if ($producto->stock !== null && $producto->stock <= 5) {
            broadcast(new StockBajoAlerta($producto, $producto->stock));
        }

        return response()->json([
            ...$producto->toArray(),
            'imagen_url' => $producto->imagen
                ? asset('storage/' . $producto->imagen)
                : null,
        ], 200);
    }


    #[OA\Delete(
        path: '/api/v1/productos/{id}',
        tags: ['Productos'],
        summary: 'Eliminar producto',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Producto eliminado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
        ]
    )]
    public function destroy(Producto $producto)
    {
        $this->authorize('delete', $producto);
        $producto->delete();

        return response()->json(null, 204);
    }
}

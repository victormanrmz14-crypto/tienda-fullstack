<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Resources\ProductoResource;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
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

    public function show(Producto $producto)
    {
        return response()->json([
            ...$producto->toArray(),
            'imagen_url' => $producto->imagen
                ? asset('storage/' . $producto->imagen)
                : null,
        ], 200);
    }

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

        return response()->json([
            ...$producto->toArray(),
            'imagen_url' => $producto->imagen
                ? asset('storage/' . $producto->imagen)
                : null,
        ], 200);
    }


    public function destroy(Producto $producto)
    {
        $this->authorize('delete', $producto);
        $producto->delete();

        return response()->json(null, 204);
    }
}

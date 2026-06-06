<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Resources\ProductoResource;
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

    public function store(Request $request)
    {
        $this->authorize('create', Producto::class);
        $datos = $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

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

    public function update(Request $request, Producto $producto)
    {
        $this->authorize('update', $producto);
        $datos = $request->validate([
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'categoria_id' => 'nullable|exists:categorias,id',
            'imagen'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

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

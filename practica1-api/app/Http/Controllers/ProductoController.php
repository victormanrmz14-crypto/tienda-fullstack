<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('id', 'desc')->get()->map(function ($producto) {
            return [
                ...$producto->toArray(),
                'imagen_url' => $producto->imagen
                    ? asset('storage/' . $producto->imagen)
                    : null,
            ];
        });

        return response()->json($productos, 200);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'imagen'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create($datos);

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
        $datos = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'imagen'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($datos);

        return response()->json([
            ...$producto->toArray(),
            'imagen_url' => $producto->imagen
                ? asset('storage/' . $producto->imagen)
                : null,
        ], 200);
    }


    public function destroy(Producto $producto)
    {
        $producto->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        return response()->json(Producto::orderBy('id', 'desc')->get(), 200);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $producto = Producto::create($datos);

        return response()->json($producto, 201);
    }

    public function show(Producto $producto)
    {
        return response()->json($producto, 200);
    }

    public function update(Request $request, Producto $producto)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $producto->update($datos);

        return response()->json($producto, 200);
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return response()->json(null, 204);
    }
}

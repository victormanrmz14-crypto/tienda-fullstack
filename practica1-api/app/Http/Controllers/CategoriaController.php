<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Resources\CategoriaResource;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Cache::remember('categorias.todas', 3600, function () {
            return CategoriaResource::collection(
                Categoria::all()
            )->toArray(request());
        });

        return response()->json(['data' => $categorias]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:255|unique:categorias',
            'descripcion' => 'nullable|string',
        ]);

        $datos['slug'] = Str::slug($datos['nombre']);

        $categoria = Categoria::create($datos);
        Cache::forget('categorias.todas');

        return new CategoriaResource($categoria);
    }

    public function show(Categoria $categoria)
    {
        return new CategoriaResource($categoria->load('productos'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $datos['slug'] = Str::slug($datos['nombre']);
        $categoria->update($datos);
        Cache::forget('categorias.todas');

        return new CategoriaResource($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        Cache::forget('categorias.todas');
        return response()->json(null, 204);
    }

    // GET /api/categorias/{categoria}/productos
    public function productos(Categoria $categoria)
    {
        return ProductoResource::collection(
            $categoria->productos()->with('categoria')->get()
        );
    }
}
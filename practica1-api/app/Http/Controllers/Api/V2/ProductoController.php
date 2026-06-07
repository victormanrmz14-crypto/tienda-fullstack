<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\ProductoController as V1ProductoController;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends V1ProductoController
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria')
            ->buscar($request->busqueda)
            ->deCategoria($request->categoria_id)
            ->rangoPrecio($request->precio_min, $request->precio_max)
            ->orderBy($request->get('orden', 'nombre'), $request->get('dir', 'asc'));

        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'LIKE', "%{$request->q}%")
                  ->orWhere('descripcion', 'LIKE', "%{$request->q}%");
            });
        }

        $productos = $query->paginate($request->get('por_pagina', 20));

        return ProductoResource::collection($productos);
    }
}

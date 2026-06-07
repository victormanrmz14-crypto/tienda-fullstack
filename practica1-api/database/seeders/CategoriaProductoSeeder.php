<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriaProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = ['Electrónica', 'Ropa', 'Hogar', 'Deportes'];

        foreach ($categorias as $nombre) {
            $cat = Categoria::create([
                'nombre' => $nombre,
                'slug'   => Str::slug($nombre),
            ]);

            // Crea 5 productos por categoría (20 en total)
            for ($i = 1; $i <= 5; $i++) {
                Producto::create([
                    'nombre'       => "$nombre producto $i",
                    'descripcion'  => "Descripción de $nombre $i",
                    'precio'       => rand(50, 5000) / 10,
                    'stock'        => rand(0, 100),
                    'categoria_id' => $cat->id,
                ]);
            }
        }
    }
}
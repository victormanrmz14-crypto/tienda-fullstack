<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Categorías base
        $categorias = collect(['Electrónica', 'Hogar', 'Deportes', 'Ropa'])
            ->mapWithKeys(fn ($nombre) => [
                $nombre => Categoria::firstOrCreate(
                    ['slug' => Str::slug($nombre)],
                    ['nombre' => $nombre]
                )->id,
            ]);

        // Exactamente 10 productos reales (los que se ven en el catálogo)
        $productos = [
            ['nombre' => 'Teclado mecánico RGB',        'descripcion' => 'Switches rojos, retroiluminación RGB y reposamuñecas.', 'precio' => 899.00,  'stock' => 15, 'categoria' => 'Electrónica'],
            ['nombre' => 'Mouse inalámbrico',           'descripcion' => 'Sensor óptico de 16000 DPI y batería recargable.',      'precio' => 549.50,  'stock' => 30, 'categoria' => 'Electrónica'],
            ['nombre' => 'Monitor 27" 144Hz',           'descripcion' => 'Panel IPS 2K, 144Hz, ideal para gaming y diseño.',      'precio' => 4599.00, 'stock' => 8,  'categoria' => 'Electrónica'],
            ['nombre' => 'Audífonos con cancelación',   'descripcion' => 'Cancelación activa de ruido y 30h de batería.',         'precio' => 1999.00, 'stock' => 12, 'categoria' => 'Electrónica'],
            ['nombre' => 'Webcam Full HD',              'descripcion' => 'Cámara 1080p con micrófono integrado y autoenfoque.',   'precio' => 749.00,  'stock' => 20, 'categoria' => 'Electrónica'],
            ['nombre' => 'Lámpara LED de escritorio',   'descripcion' => 'Luz regulable con 3 tonos y puerto USB de carga.',       'precio' => 399.00,  'stock' => 25, 'categoria' => 'Hogar'],
            ['nombre' => 'Cafetera express',            'descripcion' => 'Prepara espresso y capuchino con 15 bares de presión.',  'precio' => 2499.00, 'stock' => 10, 'categoria' => 'Hogar'],
            ['nombre' => 'Mancuernas ajustables 20kg',  'descripcion' => 'Par de mancuernas con discos intercambiables.',          'precio' => 1799.00, 'stock' => 14, 'categoria' => 'Deportes'],
            ['nombre' => 'Balón de fútbol profesional', 'descripcion' => 'Tamaño 5, cosido a máquina, apto para todo terreno.',    'precio' => 459.00,  'stock' => 40, 'categoria' => 'Deportes'],
            ['nombre' => 'Sudadera con capucha',        'descripcion' => 'Algodón premium, unisex, disponible en varias tallas.',  'precio' => 699.00,  'stock' => 35, 'categoria' => 'Ropa'],
        ];

        foreach ($productos as $p) {
            Producto::updateOrCreate(
                ['nombre' => $p['nombre']],
                [
                    'descripcion'  => $p['descripcion'],
                    'precio'       => $p['precio'],
                    'stock'        => $p['stock'],
                    'categoria_id' => $categorias[$p['categoria']],
                ]
            );
        }
    }
}

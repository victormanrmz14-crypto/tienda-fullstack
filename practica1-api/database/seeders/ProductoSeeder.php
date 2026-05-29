<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['nombre' => 'Teclado mecánico',  'descripcion' => 'Switches rojos, RGB',     'precio' => 899.00,  'stock' => 15],
            ['nombre' => 'Mouse inalámbrico',  'descripcion' => 'Sensor óptico 16000 DPI', 'precio' => 549.50,  'stock' => 30],
            ['nombre' => 'Monitor 27"',        'descripcion' => 'IPS 144Hz, 2K',           'precio' => 4599.00, 'stock' => 8],
            ['nombre' => 'Audífonos',          'descripcion' => 'Cancelación de ruido',    'precio' => 1999.00, 'stock' => 12],
        ];

        foreach ($productos as $p) {
            Producto::create($p);
        }
    }
}

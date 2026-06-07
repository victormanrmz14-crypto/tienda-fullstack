<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoMasivoSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = [
            'Teclado', 'Mouse', 'Monitor', 'Audífonos', 'Webcam',
            'Micrófono', 'Laptop', 'Tablet', 'Smartphone', 'Bocina',
            'Cargador', 'Cable HDMI', 'Disco SSD', 'Memoria RAM', 'Router',
        ];

        $adjetivos = [
            'Gamer', 'Pro', 'Inalámbrico', 'Ultra', 'Compacto',
            'Premium', 'RGB', 'Portátil', 'Profesional', 'Básico',
        ];

        $categorias = Categoria::pluck('id');

        if ($categorias->isEmpty()) {
            $this->command->warn('No hay categorías. Ejecuta primero el seeder de categorías.');
            return;
        }

        foreach ($nombres as $nombre) {
            foreach ($adjetivos as $adjetivo) {
                $nombreCompleto = "{$nombre} {$adjetivo}";

                Producto::firstOrCreate(
                    ['nombre' => $nombreCompleto],
                    [
                        'descripcion'  => "{$nombre} {$adjetivo} de alta calidad para tu setup.",
                        'precio'       => rand(10, 1000),
                        'stock'        => rand(0, 100),
                        'categoria_id' => $categorias->random(),
                    ]
                );
            }
        }

        $this->command->info('Seeder masivo completado: ' . (count($nombres) * count($adjetivos)) . ' productos procesados.');
    }
}

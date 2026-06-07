<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'       => $this->faker->words(3, true),
            'descripcion'  => $this->faker->paragraph(),
            'precio'       => $this->faker->randomFloat(2, 10, 500),
            'stock'        => $this->faker->numberBetween(0, 100),
            'categoria_id' => Categoria::factory(),
        ];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,    // 3 usuarios: admin, editor, cliente
            ProductoSeeder::class, // 4 categorías + 10 productos del catálogo
        ]);
    }
}

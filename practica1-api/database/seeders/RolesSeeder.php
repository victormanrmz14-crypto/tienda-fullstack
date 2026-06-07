<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tienda.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'rol'      => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'editor@tienda.com'],
            [
                'name'     => 'Editor',
                'password' => Hash::make('password'),
                'rol'      => 'editor',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cliente@tienda.com'],
            [
                'name'     => 'Cliente',
                'password' => Hash::make('password'),
                'rol'      => 'cliente',
            ]
        );
    }
}

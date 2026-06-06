<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_registrarse(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Juan López',
            'email'                 => 'juan@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token', 'user']);
        $this->assertDatabaseHas('users', ['email' => 'juan@test.com']);
    }

    public function test_usuario_puede_hacer_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'test@test.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'user']);
    }

    public function test_login_con_credenciales_incorrectas(): void
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'noexiste@test.com',
            'password' => 'wrongpass',
        ]);
        $response->assertStatus(401);
    }

    public function test_usuario_autenticado_puede_ver_su_perfil(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
             ->getJson('/api/me')
             ->assertOk()
             ->assertJsonStructure(['id', 'name', 'email', 'rol', 'permisos']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        $admin = User::factory()->create(['rol' => 'admin']);
        return $this->actingAs($admin, 'sanctum');
    }

    private function actingAsEditor()
    {
        $editor = User::factory()->create(['rol' => 'editor']);
        return $this->actingAs($editor, 'sanctum');
    }

    private function actingAsCliente()
    {
        $cliente = User::factory()->create(['rol' => 'cliente']);
        return $this->actingAs($cliente, 'sanctum');
    }

    public function test_puede_listar_productos(): void
    {
        Producto::factory(5)->create();
        $this->actingAsAdmin()
             ->getJson('/api/productos')
             ->assertOk()
             ->assertJsonCount(5, 'data');
    }

    public function test_puede_crear_producto(): void
    {
        $this->actingAsAdmin()
             ->postJson('/api/productos', [
                 'nombre' => 'Laptop Dell',
                 'precio' => 1299.99,
                 'stock'  => 10,
             ])
             ->assertCreated()
             ->assertJsonPath('nombre', 'Laptop Dell');

        $this->assertDatabaseHas('productos', ['nombre' => 'Laptop Dell']);
    }

    public function test_validacion_falla_sin_nombre(): void
    {
        $this->actingAsAdmin()
             ->postJson('/api/productos', [
                 'precio' => 100,
                 'stock'  => 5,
             ])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['nombre']);
    }

    public function test_puede_actualizar_producto(): void
    {
        $producto = Producto::factory()->create();
        $this->actingAsAdmin()
             ->putJson("/api/productos/{$producto->id}", [
                 'nombre' => 'Nombre Actualizado',
                 'precio' => $producto->precio,
                 'stock'  => $producto->stock,
             ])
             ->assertOk()
             ->assertJsonPath('nombre', 'Nombre Actualizado');
    }

    public function test_puede_eliminar_producto(): void
    {
        $producto = Producto::factory()->create();
        $this->actingAsAdmin()
             ->deleteJson("/api/productos/{$producto->id}")
             ->assertNoContent();

        $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
    }

    public function test_cliente_no_puede_crear(): void
    {
        $this->actingAsCliente()
             ->postJson('/api/productos', [
                 'nombre' => 'Intento cliente',
                 'precio' => 100,
                 'stock'  => 1,
             ])
             ->assertForbidden();
    }

    public function test_cliente_no_puede_eliminar(): void
    {
        $producto = Producto::factory()->create();
        $this->actingAsCliente()
             ->deleteJson("/api/productos/{$producto->id}")
             ->assertForbidden();
    }

    public function test_editor_puede_crear_pero_no_eliminar(): void
    {
        $producto = Producto::factory()->create();

        // Editor SÍ puede crear
        $this->actingAsEditor()
             ->postJson('/api/productos', [
                 'nombre' => 'Producto Editor',
                 'precio' => 50,
                 'stock'  => 3,
             ])
             ->assertCreated();

        // Editor NO puede eliminar
        $this->actingAsEditor()
             ->deleteJson("/api/productos/{$producto->id}")
             ->assertForbidden();
    }
}

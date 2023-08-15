<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminCreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_product_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/products', [
            'name'  => 'Product 1',
            'description' => 'Product description',
            'price' => 123.45,
            'stock_quantity' => 50
        ]);

        $response->assertStatus(201);
        $response->assertJson(['data' => [
            'name'  => 'Product 1',
            'description' => 'Product description',
            'price' => 123.45,
            'stock_quantity' => 50
        ]]);
        $this->assertDatabaseHas('products', [
            'name'  => 'Product 1',
            'description' => 'Product description',
            'price' => 123.45,
            'stock_quantity' => 50
        ]);
    }

    public function test_create_product_failed_due_to_incorrect_data_type_of_price_and_stock_quantity()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/api/v1/admin/products', [
            'name'  => 'Product 1',
            'description' => 'Product description',
            'price' => 123,
            'stock_quantity' => 'Fifty'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price', 'stock_quantity']]);
    }

    public function test_create_product_as_a_user_role()
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->postJson('/api/v1/admin/products', [
            'name'  => 'Product 1',
            'description' => 'Product description',
            'price' => 123.45,
            'stock_quantity' => 50
        ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'You are not an Admin to create a product.']);
    }
}

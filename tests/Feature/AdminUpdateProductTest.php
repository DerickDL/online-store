<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminUpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_product_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->putJson('/api/v1/admin/products/'.$product->id, [
            'name'  => 'Updated Product',
            'description' => 'Updated Product description',
            'price' => 123.45,
            'stock_quantity' => 50
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseHas('products', [
            'name'  => 'Updated Product',
            'description' => 'Updated Product description',
            'price' => 123.45,
            'stock_quantity' => 50
        ]);
    }

    public function test_update_product_failed_due_to_incorrect_data_type_of_price_and_stock_quantity()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->putJson('/api/v1/admin/products/'.$product->id, [
            'name'  => 'Updated Product',
            'description' => 'Updated Product description',
            'price' => 123,
            'stock_quantity' => 'Fifty'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price', 'stock_quantity']]);
    }

    public function test_update_product_as_a_user_role()
    {
        $user = User::factory()->create(['role' => 'user']);
        $product = Product::factory()->create();
        
        $response = $this->actingAs($user)->putJson('/api/v1/admin/products/'.$product->id, [
            'name'  => 'Product 1',
            'description' => 'Product description',
            'price' => 123.45,
            'stock_quantity' => 50
        ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'You are not an Admin to update a product.']);
    }
}

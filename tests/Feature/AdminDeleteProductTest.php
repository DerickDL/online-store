<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminDeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_product_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->deleteJson('/api/v1/admin/products/'.$product->id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_delete_product_as_a_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/v1/admin/products/'.$product->id);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'You are not an Admin to delete a product.']);
        $this->assertDatabaseHas('products', [
            'name'  => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock_quantity' => $product->stock_quantity,
        ]);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class GetProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_paginated_products()
    {
        $user = User::factory()->create();
        $product = Product::factory(25)->create();

        $response = $this->actingAs($user)->getJson('/api/v1/products');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.last_page', 3);
    }

    public function test_get_a_specific_product()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/products/'.$product1->id);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $product1->id,
            'name' => $product1->name,
            'description' => $product1->description,
            'price' => number_format($product1->price, 2),
            'stock_quantity' => $product1->stock_quantity,
        ]);
    }

    public function test_get_a_non_existing_product()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/products/80');

        $response->assertStatus(404);
        $response->assertJsonStructure(['error']);
    }
}

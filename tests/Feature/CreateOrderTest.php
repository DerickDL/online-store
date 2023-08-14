<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_successfully()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['stock_quantity' => 100]);
        $product2 = Product::factory()->create(['stock_quantity' => 100]);
        $product3 = Product::factory()->create(['stock_quantity' => 100]);
        $product4 = Product::factory()->create(['stock_quantity' => 100]);
        $product5 = Product::factory()->create(['stock_quantity' => 100]);
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product1->id, ['quantity' => 20]);
        $cart->items()->attach($product2->id, ['quantity' => 10]);
        $cart->items()->attach($product3->id, ['quantity' => 30]);
        $cart->items()->attach($product4->id, ['quantity' => 10]);
        $cart->items()->attach($product5->id, ['quantity' => 50]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'stock_quantity' => 80
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'stock_quantity' => 90
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product3->id,
            'stock_quantity' => 70
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product4->id,
            'stock_quantity' => 90
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product5->id,
            'stock_quantity' => 50
        ]);
    }

    public function test_create_order_fail_due_to_insufficient_stock()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['stock_quantity' => 100]);
        $product2 = Product::factory()->create(['stock_quantity' => 100]);
        $product3 = Product::factory()->create(['stock_quantity' => 100]);
        $product4 = Product::factory()->create(['stock_quantity' => 100]);
        $product5 = Product::factory()->create(['stock_quantity' => 100]);
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product1->id, ['quantity' => 20]);
        $cart->items()->attach($product2->id, ['quantity' => 10]);
        $cart->items()->attach($product3->id, ['quantity' => 300]);
        $cart->items()->attach($product4->id, ['quantity' => 10]);
        $cart->items()->attach($product5->id, ['quantity' => 50]);

        $response = $this->actingAs($user)->postJson('/api/v1/orders');

        $response->assertStatus(422);
        $response->assertJson(['error' => 'Product ' . $product3->name . ' stock is insufficient.']);
        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'stock_quantity' => 100
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'stock_quantity' => 100
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product3->id,
            'stock_quantity' => 100
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product4->id,
            'stock_quantity' => 100
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product5->id,
            'stock_quantity' => 100
        ]);
    }
}

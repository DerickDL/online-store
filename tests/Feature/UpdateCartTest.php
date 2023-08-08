<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;

class UpdateCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_a_cart_item_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product->id, ['quantity' => 100]);

        $response = $this->actingAs($user)->putJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 20
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $user->cart->id,
            'product_id' => $product->id,
            'quantity' => 20
        ]);
    }

    public function test_update_a_cart_non_existing_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product->id, ['quantity' => 100]);

        $response = $this->actingAs($user)->putJson('/api/v1/cart/items', [
            'product_id' => 403,
            'quantity' => 20
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['product_id']]);
    }
}

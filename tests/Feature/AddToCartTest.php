<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;

class AddToCartTest extends TestCase
{

    use RefreshDatabase;

    public function test_add_to_cart_new_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 20
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $user->cart->id,
            'product_id' => $product->id,
            'quantity' => 20
        ]);
    }


    public function test_add_to_cart_previously_added_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product->id, ['quantity' => 20]);

        $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $user->cart->id,
            'product_id' => $product->id,
            'quantity' => 22
        ]);
    }

    public function test_add_to_cart_non_existing_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/cart/items', [
            'product_id' => 75,
            'quantity' => 2
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['product_id']]);
    }
}

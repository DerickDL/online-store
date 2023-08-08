<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;

class DeleteCartItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_a_cart_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product->id, ['quantity' => 25]);

        $response = $this->actingAs($user)->delete('/api/v1/cart/items', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id,
        ]);
    }

    public function test_delete_a_cart_item_without_product_id(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product->id, ['quantity' => 25]);

        $response = $this->actingAs($user)->deleteJson('/api/v1/cart/items');

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['product_id']]);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;

class ShowCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_cart_items_of_the_cart_owner()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product->id, ['quantity' => 20]);

        $response = $this->actingAs($user)->getJson('/api/v1/cart/items');

        $response->assertStatus(200);
        $response->assertJsonPath('0.id', $product->id);
        $response->assertJsonPath('0.pivot.quantity', 20);
    }
}

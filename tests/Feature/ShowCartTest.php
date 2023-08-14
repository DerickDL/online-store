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

        $response = $this->actingAs($user)->getJson('/api/v1/cart/items/'.$cart->id);

        $response->assertStatus(200);
        $response->assertJsonPath('items.0.id', $product->id);
        $response->assertJsonPath('items.0.pivot.quantity', 20);
    }

    public function test_get_cart_items_of_the_cart_owned_by_others()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user1->id]);
        $cart->items()->attach($product->id, ['quantity' => 20]);

        $response = $this->actingAs($user2)->getJson('/api/v1/cart/items/'.$cart->id);

        $response->assertStatus(403);
        $response->assertJsonStructure(['message']);
    }

    public function test_get_cart_total_price()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->attach($product1->id, ['quantity' => 20]);
        $cart->items()->attach($product2->id, ['quantity' => 20]);

        $response = $this->actingAs($user)->getJson('/api/v1/cart/items/'.$cart->id);

        $response->assertStatus(200);
        $response->assertJsonPath('items.0.id', $product1->id);
        $response->assertJsonPath('items.1.id', $product2->id);
        $response->assertJsonPath('total_price', ($product1->price*20) + ($product2->price*20));
    }
}

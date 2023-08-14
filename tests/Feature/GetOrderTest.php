<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_list_of_orders_and_total_amount()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['stock_quantity' => 100]);
        $product2 = Product::factory()->create(['stock_quantity' => 100]);
        $product3 = Product::factory()->create(['stock_quantity' => 100]);
        $product4 = Product::factory()->create(['stock_quantity' => 100]);
        $product5 = Product::factory()->create(['stock_quantity' => 100]);
        $order = Order::create(['user_id' => $user->id]);
        $order->items()->attach($product1->id, ['quantity' => 20, 'unit_price' => $product1->price]);
        $order->items()->attach($product2->id, ['quantity' => 10, 'unit_price' => $product2->price]);
        $order->items()->attach($product3->id, ['quantity' => 30, 'unit_price' => $product3->price]);
        $order->items()->attach($product4->id, ['quantity' => 10, 'unit_price' => $product4->price]);
        $order->items()->attach($product5->id, ['quantity' => 50, 'unit_price' => $product5->price]);

        $response = $this->actingAs($user)->getJson('/api/v1/orders/'.$order->id);

        $response->assertStatus(200);
        $response->assertJsonStructure(['orders', 'total_amount']);
        $response->assertJson([
            'total_amount' => $order->items->sum(function ($item) {
                return $item->pivot->quantity * $item->pivot->unit_price;
            })
        ]);
    }

    public function test_get_non_existing_orders()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson('/api/v1/orders/200');

        $response->assertStatus(404);
    }

    public function test_get_order_of_other_user()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $product1 = Product::factory()->create(['stock_quantity' => 100]);
        $product2 = Product::factory()->create(['stock_quantity' => 100]);
        $product3 = Product::factory()->create(['stock_quantity' => 100]);
        $product4 = Product::factory()->create(['stock_quantity' => 100]);
        $product5 = Product::factory()->create(['stock_quantity' => 100]);
        $order = Order::create(['user_id' => $user->id]);
        $order->items()->attach($product1->id, ['quantity' => 20, 'unit_price' => $product1->price]);
        $order->items()->attach($product2->id, ['quantity' => 10, 'unit_price' => $product2->price]);
        $order->items()->attach($product3->id, ['quantity' => 30, 'unit_price' => $product3->price]);
        $order->items()->attach($product4->id, ['quantity' => 10, 'unit_price' => $product4->price]);
        $order->items()->attach($product5->id, ['quantity' => 50, 'unit_price' => $product5->price]);

        $response = $this->actingAs($user2)->getJson('/api/v1/orders/'.$order->id);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'This is not your order.'
        ]);
    }
}

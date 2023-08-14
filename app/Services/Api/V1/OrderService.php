<?php

namespace App\Services\Api\V1;

use App\Http\Requests\OrderRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(OrderRequest $request)
    {
        $user = $request->user();
        $cart = $user->cart;

        DB::beginTransaction();
        try {
            $order = $this->createOrder($user);
            $this->addCartItemsToOrder($order, $cart);
            DB::commit();
            return response()->json(['message' => 'Order created Successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    private function createOrder(User $user)
    {
        return Order::create(['user_id' => $user->id]);
    }

    public function addCartItemsToOrder(Order $order, Cart $cart)
    {
        $items = $cart->items;
        foreach ($items as $item) {
            if (!$this->canAddToOrder($item)) {
                throw new \Exception('Product ' .$item->name. ' stock is insufficient.');
            }
            $this->addToOrder($order, $item, $cart);
            $this->deleteCartItem($cart, $item);
            $this->updateStockQuantity($item);
        }

    }

    private function canAddToOrder(Product $item)
    {
        return ($item->pivot->quantity <= $item->stock_quantity);
    }

    private function addToOrder(Order $order, Product $item, Cart $cart)
    {
        $order->items()->attach($item->id, ['quantity' => $item->pivot->quantity, 'unit_price' => $item->price]);
    }

    private function deleteCartItem(Cart $cart, Product $item)
    {
        $cart->items()->detach($item->id);
    }

    private function updateStockQuantity(Product $item)
    {
        $item->stock_quantity -= $item->pivot->quantity;
        $item->save();
    }
}

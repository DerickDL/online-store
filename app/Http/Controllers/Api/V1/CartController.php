<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\CartRequest;
use App\Http\Requests\DeleteCartRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class CartController extends Controller
{
    /**
     * Create a new Cart or add a quantity to existing cart item
     */
    public function add(CartRequest $cartRequest)
    {
        try {
            $request = $cartRequest->validated();
            $cart = Cart::firstOrCreate(['user_id' => $cartRequest->user()->id]);
            $product = Product::findOrFail($request['product_id']);
            
            $existingItem = $cart->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $existingItem->pivot->quantity += $request['quantity'];
                $existingItem->pivot->save();
            } else {
                $cart->items()->attach($product->id, ['quantity' => $request['quantity']]);
            }

            return response()->json(['message' => 'Product added to cart successfully'], 201);
        } catch(ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }

    /**
     * Display the specified cart
     */
    public function view(Request $request, Cart $cart)
    {
        return response()->json($cart->items);
    }

    /**
     * Update a cart item
     */
    public function update(CartRequest $cartRequest)
    {
        $cart = $cartRequest->user()->cart;
        $request = $cartRequest->validated();
        $cart->items()->syncWithoutDetaching([$request['product_id'] => ['quantity' => $request['quantity']]]);
        return response()->json(['message' => 'Cart item updated successfully'], 200);
    }

    /**
     * Remove a specified cart item
     */
    public function delete(DeleteCartRequest $request)
    {
        try {
            $user = $request->user();
            $cart = $user->cart;
            $productRequest = $request->validated();
            $product = Product::findOrFail($productRequest['product_id']);
            $cart->items()->detach($product);
            return response()->json(['message' => 'Item removed from cart']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
    }
}

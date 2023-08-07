<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\CartRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\QueryException;

class CartController extends Controller
{
    /**
     * create a new Cart or add a quantity to existing cart item
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
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        return response()->json($cart->items);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CartRequest $cartRequest, Cart $cart)
    {
        $request = $cartRequest->validated();
        $cart->items()->syncWithoutDetaching([$request['product_id'] => ['quantity' => $request['quantity']]]);
        return response()->json(['message' => 'Cart item updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, string $product_id)
    {
        try {
            $user = $request->user();
            $cart = $user->cart;
            $product = Product::findOrFail($product_id);
            $cart->items()->detach($product);
            return response()->json(['message' => 'Item removed from cart']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
    }
}

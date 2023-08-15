<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;

class ProductController extends Controller
{
    public function create(ProductRequest $request)
    {
        try {
            $this->authorize('create', Product::class);
            return new ProductResource(Product::create($request->validated()));
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function update(ProductUpdateRequest $request, string $id)
    {
        try {
            $this->authorize('update', Product::class);

            $product = Product::findOrFail($id);
            $productData = $request->validated();

            $fieldsToCheck = ['name', 'description', 'price', 'stock_quantity'];

            foreach ($fieldsToCheck as $field) {
                if (array_key_exists($field, $productData)) {
                    $product->{$field} = $productData[$field];
                };
            }

            $product->save();

            return response()->json(['message' => 'Product updated successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found.'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->authorize('delete', Product::class);

            $product = Product::findOrFail($id);
            $product->delete();
            
            return response()->json(['message' => 'Product updated successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product to delete not found.'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}

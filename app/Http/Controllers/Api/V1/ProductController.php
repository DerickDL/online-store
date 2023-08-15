<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ProductController extends Controller
{
    /**
     * Get all the products
     */
    public function index()
    {
        return ProductResource::collection(Product::paginate(10));
    }

    /**
     * Get a specific product
     */
    public function view(int $productId)
    {
        try{
            return new ProductResource(Product::findOrFail($productId));
        } catch(ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        }
    }
}

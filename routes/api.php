<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Carts routes
    Route::prefix('cart')->group(function () {
        Route::post('/items', [CartController::class, 'create']);
        Route::put('/items', [CartController::class, 'update']);
        Route::get('/items/{cart}', [CartController::class, 'view'])->can('view', 'cart');
        Route::delete('/items/{product}', [CartController::class, 'destroy']);
    });

    // Products routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{product}', [ProductController::class, 'view']);
    });

    // Orders Routes
    Route::prefix('orders')->middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [OrderController::class, 'create']);
        Route::get('/{order}', [OrderController::class, 'view'])->can('view', 'order');
    });

    Route::prefix('admin/products')->middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [AdminProductController::class, 'create']);
        Route::put('/{product}', [AdminProductController::class, 'update']);
        Route::delete('/{product}', [AdminProductController::class, 'destroy']);
    });

});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


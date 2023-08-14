<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Api\V1\OrderService;

class OrderController extends Controller
{
    public function create(OrderRequest $request, OrderService $orderService)
    {
        return $orderService->create($request);
    }

    public function view(Order $order)
    {
        return response()->json([
            'orders' => $order->items,
            'total_amount' => $order->total_amount
        ]);
    }
}

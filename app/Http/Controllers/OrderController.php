<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Services\MapboxService;
use App\Services\OrderDetailService;
use App\Services\OrderService;
use App\Services\UserService;

class OrderController extends Controller
{
    protected $orderService;
    protected $mapboxService;
    protected $userService;

    protected $orderDetailService;

    public function __construct(
        OrderService $orderService,
        MapboxService $mapboxService,
        UserService $userService,
        OrderDetailService $orderDetailService
    ) {
        $this->orderService = $orderService;
        $this->mapboxService = $mapboxService;
        $this->userService = $userService;
        $this->orderDetailService = $orderDetailService;
    }

    public function getOrdersByUser()
    {
        try {
            $order = $this->orderService->getOrdersByUser();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ], 200);
    }

    public function deleteOrderByUser($id)
    {
        try {
            $order = $this->orderService->deleteOrderByUser($id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'delete' => 'Success',
        ], 200);
    }

    public function updateStatusFrom3To4()
    {
        $products = Order::all();

        foreach ($products as $product) {
            $orderDetails = OrderDetail::where('order_id', $product->id)->get();
            // $cost = $this->userService->getCostShip($product->user_id, $product->seller_id);
            // $calculateCost = $this->orderDetailService->calculateCost($cost);
            // $price = 0;
            // $type = 0;

            // foreach ($orderDetails as $orderDetail) {
            //     $item = Item::where('id', $orderDetail->item_id)->first();

            //     if ($item) {
            //         $price += $item->price_type * $orderDetail->count;
            //         $type += $item->type * $orderDetail->count;
            //     }
            // }
            // $product->cost = $calculateCost;
            $product->shipping_money = $product->cost * $product->total_quantity;
            $product->save();
        }
    }
}

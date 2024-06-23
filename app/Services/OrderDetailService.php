<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderDetail\OrderDetailRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderDetailService
{
    protected $orderDetailRepository;

    protected $orderRepository;

    protected $userService;

    public function __construct(
        OrderDetailRepository $orderDetailRepository,
        OrderRepository $orderRepository,
        UserService $userService,
    ) {
        $this->orderDetailRepository = $orderDetailRepository;
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
    }

    public function createOrderDetailsByUser($validate)
    {
        $atr1 = [
            'item_id' => $validate['item_id'],
            'count' => $validate['count'],
        ];
        $userId = auth()->id();
        DB::beginTransaction();
        try {
            $order = Order::where('user_id', $userId)
            ->where('seller_id', $validate['seller_id'])
            ->where('status', '=', 1)->first();
            if (!$order) {
                $km = $this->userService->getCostShip($userId, $validate['seller_id']);
                $cost = $this->calculateCost($km);
                $atr = [
                'total_price' => 0,
                'total_quantity' => 0,
                'user_id' => $userId,
                'seller_id' => $validate['seller_id'],
                'cost' => $cost,
                'shipping_money' => 0,
                'status' => 1
                ];
                $order = $this->orderRepository->create($atr);
            }
            $orderDetail = OrderDetail::where('order_id', $order->id)
            ->where('item_id', $validate['item_id'])
            ->first();
            if ($orderDetail) {
                $orderDetail->count += $validate['count'];
                $orderDetail->save();
            } else {
                $atr1['order_id'] = $order->id;
                $orderDetail = $this->orderDetailRepository->create($atr1);
            }
            $item = Item::where('id', $validate['item_id'])->first();
            $order->total_price += $validate['count'] * $item->price_type;
            $order->total_quantity += $validate['count'] * $item->type;
            $order->shipping_money += $validate['count'] * $item->type * $order->cost;
            $order->save();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return [
            'order' => $order,
            'orderDetail' => $orderDetail
        ];
    }

    public function updateOrderDetailsByUser($id, $validate)
    {
        $userId = auth()->id();
        $orderDetail = OrderDetail::where('id', $id)
            ->first();
        if (!$orderDetail) {
            throw new Exception('This order Detail does not exist');
        }
        $order = Order::where('id', $orderDetail->order_id)
            ->where('user_id', '=', $userId)->first();
        if (!$order) {
            throw new Exception('This order does not exist');
        }
        DB::beginTransaction();
        try {
            $item = Item::where('id', $orderDetail->item_id)->first();
            $a = $validate['count'] - $orderDetail->count;
            $order->total_price += $a * $item->price_type;
            $order->total_quantity += $a * $item->type;
            $order->shipping_money += $a * $item->type * $order->cost;
            $order->save();
            $update = $this->orderDetailRepository->update($id, $validate);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return $update;
    }

    public function deleteOrderDetailsByUser($id)
    {
        $userId = auth()->id();
        $orderDetail = OrderDetail::where('id', $id)
            ->first();
        if (!$orderDetail) {
            throw new Exception('This order Detail does not exist');
        }
        $order = Order::where('id', $orderDetail->order_id)
            ->where('user_id', '=', $userId)->first();
        if (!$order) {
            throw new Exception('This order does not exist');
        }
        DB::beginTransaction();
        try {
            $item = Item::where('id', $orderDetail->item_id)->first();
            $order->total_price -= $orderDetail->count * $item->price_type;
            $order->total_quantity -=  $orderDetail->count * $item->type;
            $order->shipping_money -= $orderDetail->count * $item->type * $order->cost;
            $order->save();
            $delete = $this->orderDetailRepository->delete($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return $delete;
    }

    public function calculateCost($distance)
    {
        switch (true) {
            case ($distance < 50):
                return 500;
            case ($distance >= 50 && $distance < 100):
                return 800;
            case ($distance >= 100 && $distance < 300):
                return 1000;
            case ($distance >= 300 && $distance < 500):
                return 1200;
            default:
                return 1500;
        }
    }
}

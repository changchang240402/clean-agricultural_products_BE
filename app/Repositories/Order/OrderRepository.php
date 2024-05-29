<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Repositories\Order\OrderRepositoryInterface;
use Exception;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function getModel()
    {
        return Order::class;
    }

    public function getOrdersByUser()
    {
        $userId = auth()->id();

        return $this->model
        ->where('user_id', $userId)
        ->where('status', '=', 1)
        ->with([
            'seller' => function ($query) {
                $query->select('id', 'name');
            }
        ])
        ->with('orderDetails.item')
        ->get();
    }

    public function totalOrderDetailByUser()
    {
        $userId = auth()->id();

        return $this->model
        ->where('user_id', $userId)
        ->where('status', '=', 1)
        ->withCount('orderDetails  as total_order_details')
        ->get()
        ->sum('order_details_count');
    }
}

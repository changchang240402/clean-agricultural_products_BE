<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderDetail\OrderDetailRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $orderDetailRepository;

    protected $orderRepository;

    protected $mapboxService;

    public function __construct(
        OrderDetailRepository $orderDetailRepository,
        OrderRepository $orderRepository,
        MapboxService $mapboxService
    ) {
        $this->orderDetailRepository = $orderDetailRepository;
        $this->orderRepository = $orderRepository;
        $this->mapboxService = $mapboxService;
    }

    public function getOrdersByUser()
    {
        return $this->orderRepository->getOrdersByUser();
    }

    public function deleteOrderByUser($id)
    {
        $userId = auth()->id();
        $order = Order::where('id', $id)
            ->where('user_id', '=', $userId)->first();
        if (!$order) {
            throw new Exception('This order does not exist');
        }

        DB::beginTransaction();
        try {
            $orderDetail = $this->orderDetailRepository->getOrderDetailIdByUser($id);
            $orderDetailIds = $orderDetail->pluck('id')->toArray();
            $deleteDetail = $this->orderDetailRepository->deleteMultipleOrderDetail($orderDetailIds);
            $delete = $this->orderRepository->delete($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        DB::commit();
        return [
            'delete' => $delete,
            'deleteDetail' => $deleteDetail
        ];
    }
}

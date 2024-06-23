<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use App\Repositories\OrderDetail\OrderDetailRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderService
{
    private const PAGINATE_PER_PAGE = 10;
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

    public function totalOrderDetailByUser()
    {
        return $this->orderRepository->totalOrderDetailByUser();
    }


    public function updateBill()
    {
        $orders =  $this->orderRepository->getOrderBill();
        return $orders;
    }

    public function updateBillById($userId, $id)
    {
        return $this->orderRepository->getOrderCancelTrader($userId, $id);
    }

    public function statisticsOrder($userId, $role)
    {
        return $this->orderRepository->statisticsOrder($userId, $role);
    }

    public function getOrder(
        int $userId,
        int $role,
        int $page,
        int $status = null,
        int $time = null
    ) {
        $orders = $this->orderRepository->getOrder($userId, $role);
        if ($status) {
            $orders = $this->filterByStatus($orders, $status);
        }
        if ($time) {
            $orders = $this->filterByTime($orders, $time);
        }
        if ($orders->isEmpty()) {
            throw new Exception('Shop not found');
        }
        $perPage = self::PAGINATE_PER_PAGE;
        $ordersPerPage = $orders->forPage($page, $perPage);
        $paginatedOrders = new LengthAwarePaginator(
            $ordersPerPage->values()->all(),
            $orders->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginatedOrders;
    }

    private function filterByTime($orders, $time)
    {
        $filteredOrders = collect();

        $startDate = match ($time) {
            1 => Carbon::now()->startOfMonth(),
            2 => Carbon::now()->subMonths(3)->startOfMonth(),
            3 => Carbon::now()->subMonths(6)->startOfMonth(),
            4 => Carbon::now()->startOfYear(),
            default => null,
        };

        if ($startDate) {
            foreach ($orders as $order) {
                $orderDate = Carbon::parse($order['order_date']);
                if ($orderDate >= $startDate) {
                    $filteredOrders->push($order);
                }
            }
        } else {
            $filteredOrders = $orders;
        }

        return $filteredOrders;
    }

    private function filterByStatus($orders, $status)
    {
        return $status ? $orders->filter(function ($order) use ($status) {
            if ($order['status'] === $status) {
                return true;
            }
            return false;
        }) : $orders;
    }

    public function filterOrder(int $userId, int $role, int $page, array $filter)
    {
        $status = null;
        $time = null;
        if (isset($filter['status'])) {
            $status = $filter['status'];
        }
        if (isset($filter['time'])) {
            $time = $filter['time'];
        }
        return $this->getOrder($userId, $role, $page, $status, $time);
    }

    public function orderById($id, $userId)
    {
        return $this->orderRepository->orderById($id, $userId);
    }
}

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
            ->withCount('orderDetails')
            ->get()->sum('order_details_count');
    }

    public function getOrderBill()
    {
        $userId = auth()->id();

        return $this->model
            ->where('user_id', $userId)
            ->where('status', '=', 1)
            ->with([
                'seller' => function ($query) {
                    $query->select('id', 'address', 'email');
                }
            ])
            ->get();
    }

    public function getOrderCancelTrader($userId, $id)
    {
        return $this->model
            ->where('id', $id)
            ->where('trader_id', '=', $userId)
            ->with([
                'seller' => function ($query) {
                    $query->select('id', 'address', 'email');
                }
            ])
            ->first();
    }

    public function statisticsOrder($userId, $role)
    {
        $totalMoney = 0;
        $totalComplete = 0;
        $totalTransported = 0;
        $totalCancel = 0;
        switch ($role) {
            case 0:
                $totalMoney = $this->model->where('status', '!=', 1)->sum('total_price');
                $totalComplete = $this->model->where('status', '=', 4)->count();
                $totalTransported = $this->model->where('status', '=', 3)->count();
                $totalCancel = $this->model->where('status', '=', 6)->count();
                break;
            case 1:
                $totalMoney = $this->model->where('user_id', $userId)->where('status', '!=', 1)->sum('total_price');
                $totalComplete = $this->model->where('user_id', $userId)->where('status', '=', 4)->count();
                $totalTransported = $this->model->where('user_id', $userId)->where('status', '=', 3)->count();
                $totalCancel = $this->model->where('user_id', $userId)->where('status', '=', 6)->count();
                break;
            case 2:
                $totalMoney = $this->model->where('seller_id', $userId)->where('status', '!=', 1)->sum('total_price');
                $totalComplete = $this->model->where('seller_id', $userId)->where('status', '=', 4)->count();
                $totalTransported = $this->model->where('seller_id', $userId)->where('status', '=', 3)->count();
                $totalCancel = $this->model->where('user_id', $userId)->where('status', '=', 6)->count();
                break;
            case 3:
                $totalMoney = $this->model->where('trader_id', $userId)->where('status', '!=', 1)->sum('total_price');
                $totalComplete = $this->model->where('trader_id', $userId)->where('status', '=', 4)->count();
                $totalTransported = $this->model->where('trader_id', $userId)->where('status', '=', 3)->count();
                $totalCancel = $this->model->where('user_id', $userId)->where('status', '=', 6)->count();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return [
            'totalMoney' => $totalMoney,
            'totalComplete' => $totalComplete,
            'totalTransported' => $totalTransported,
            'totalCancel' => $totalCancel
        ];
    }

    public function getOrder($userId, $role)
    {
        $orders = [];
        switch ($role) {
            case 0:
                $orders = $this->model->where('status', '!=', 1)->orderBy('order_date', 'desc')->get();
                break;
            case 1:
                $orders = $this->model
                    ->where('user_id', $userId)
                    ->where('status', '!=', 1)
                    ->orderBy('order_date', 'desc')
                    ->get();
                break;
            case 2:
                $orders = $this->model
                    ->where('seller_id', $userId)
                    ->where('status', '!=', 1)
                    ->orderBy('order_date', 'desc')
                    ->get();
                break;
            case 3:
                $orders = $this->model
                    ->where('trader_id', $userId)
                    ->where('status', '!=', 1)
                    ->orderBy('order_date', 'desc')
                    ->get();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return $orders;
    }

    public function orderById($id, $userId)
    {
        return $this->model->where('id', $id)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('seller_id', $userId)
                    ->orWhere('trader_id', $userId);
            })
            ->with('orderDetails.item')
            ->with('seller')
            ->with('user')
            ->with('trader')
            ->first();
    }

    public function totalMoneyByUserId($userId, $role, $month, $year)
    {
        $money = 0;
        $money_now = 0;
        switch ($role) {
            case 0:
                $price = $this->model->where('status', '!=', 1)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money = $price->total_price_sum + $price->shipping_money_sum;
                $totals = $this->model->where('status', '!=', 1)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money_now = $totals->total_price_sum + $totals->shipping_money_sum;
                break;
            case 2:
                $money = $this->model->where('status', '!=', 1)
                    ->where('seller_id', $userId)
                    ->sum('total_price');
                $money_now = $this->model->where('status', '!=', 1)
                    ->where('seller_id', $userId)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->sum('total_price');
                break;
            case 3:
                $money = $this->model->where('status', '!=', 1)
                    ->where('trader_id', $userId)
                    ->sum('shipping_money');
                $money_now = $this->model->where('status', '!=', 1)
                    ->where('trader_id', $userId)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->sum('shipping_money');
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return [
            'money' => $money,
            'money_now' => $money_now
        ];
    }
    public function totalOrderByUserId($userId, $role, $month, $year)
    {
        $order = 0;
        $order_now = 0;
        switch ($role) {
            case 0:
                $order = $this->model->where('status', '!=', 1)->count();
                $order_now = $this->model->where('status', '!=', 1)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->count();
                break;
            case 2:
                $order = $this->model->where('status', '!=', 1)
                    ->where('seller_id', $userId)
                    ->count();
                $order_now = $this->model->where('status', '!=', 1)
                    ->where('seller_id', $userId)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->count();
                break;
            case 3:
                $order = $this->model->where('status', '!=', 1)
                    ->where('trader_id', $userId)
                    ->count();
                $order_now = $this->model->where('status', '!=', 1)
                    ->where('trader_id', $userId)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->count();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return [
            'order' => $order,
            'order_now' => $order_now
        ];
    }

    public function totalQuantityByUserId($userId, $role, $month, $year)
    {
        $quantity = 0;
        $quantity_now = 0;
        switch ($role) {
            case 2:
                $quantity = $this->model->where('status', '!=', 1)
                    ->where('seller_id', $userId)
                    ->sum('total_quantity');
                $quantity_now = $this->model->where('status', '!=', 1)
                    ->where('seller_id', $userId)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->sum('total_quantity');
                break;
            case 3:
                $quantity = $this->model->where('status', '!=', 1)
                    ->where('trader_id', $userId)
                    ->sum('total_quantity');
                $quantity_now = $this->model->where('status', '!=', 1)
                    ->where('trader_id', $userId)
                    ->whereYear('order_date', $year)
                    ->whereMonth('order_date', $month)
                    ->sum('total_quantity');
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return [
            'quantity' => $quantity,
            'quantity_now' => $quantity_now
        ];
    }
    public function totalMoneyByMonth($userId, $role, $month, $year)
    {
        $money_received = 0;
        $money_cancellation = 0;
        $money_cancel = 0;
        switch ($role) {
            case 0:
                $price_received = $this->model->where('status', '=', 4)
                    ->whereYear('received_date', $year)
                    ->whereMonth('received_date', $month)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money_received = $price_received->total_price_sum + $price_received->shipping_money_sum;
                $price_cancellation = $this->model->where('status', '=', 5)
                    ->whereYear('received_date', $year)
                    ->whereMonth('received_date', $month)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money_cancellation = $price_cancellation->total_price_sum + $price_cancellation->shipping_money_sum;
                $price_cancel = $this->model
                    ->whereYear('order_cancellation_date', $year)
                    ->whereMonth('order_cancellation_date', $month)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money_cancel = $price_cancel->total_price_sum + $price_cancel->shipping_money_sum;
                break;
            case 2:
                $money_received = $this->model->where('status', '=', 4)
                    ->where('seller_id', $userId)
                    ->whereYear('received_date', $year)
                    ->whereMonth('received_date', $month)
                    ->sum('total_price');
                $money_cancellation = $this->model->where('status', '=', 5)
                    ->where('seller_id', $userId)
                    ->whereYear('received_date', $year)
                    ->whereMonth('received_date', $month)
                    ->sum('total_price');
                $money_cancel = $this->model
                    ->where('seller_id', $userId)
                    ->whereYear('order_cancellation_date', $year)
                    ->whereMonth('order_cancellation_date', $month)
                    ->sum('total_price');
                break;
            case 3:
                $price_received = $this->model->where('status', '=', 4)
                    ->where('trader_id', $userId)
                    ->whereYear('received_date', $year)
                    ->whereMonth('received_date', $month)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money_received = $price_received->total_price_sum + $price_received->shipping_money_sum;
                $price_cancellation = $this->model->where('status', '=', 5)
                    ->where('trader_id', $userId)
                    ->whereYear('received_date', $year)
                    ->whereMonth('received_date', $month)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money_cancellation = $price_cancellation->total_price_sum + $price_cancellation->shipping_money_sum;
                $price_cancel = $this->model
                    ->where('trader_id', $userId)
                    ->whereYear('order_cancellation_date', $year)
                    ->whereMonth('order_cancellation_date', $month)
                    ->selectRaw('SUM(total_price) as total_price_sum, SUM(shipping_money) as shipping_money_sum')
                    ->first();
                $money_cancel = $price_cancel->total_price_sum + $price_cancel->shipping_money_sum;
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return [
            'time' => $month . '/' . $year,
            'money_received' => $money_received,
            'money_cancellation' => $money_cancellation,
            'money_cancel' => $money_cancel
        ];
    }

    public function totalOrderByStatus($userId, $role)
    {
        $prepare = 0;
        $transportation = 0;
        $received = 0;
        $cancellation = 0;
        $cancel = 0;
        switch ($role) {
            case 0:
                $prepare = $this->model->where('status', '=', 2)->count();
                $transportation = $this->model->where('status', '=', 3)->count();
                $received = $this->model->where('status', '=', 4)->count();
                $cancellation = $this->model->where('status', '=', 5)->count();
                $cancel = $this->model->where('status', '=', 6)->count();
                break;
            case 2:
                $prepare = $this->model->where('status', '=', 2)->where('seller_id', $userId)->count();
                $transportation = $this->model->where('status', '=', 3)->where('seller_id', $userId)->count();
                $received = $this->model->where('status', '=', 4)->where('seller_id', $userId)->count();
                $cancellation = $this->model->where('status', '=', 5)->where('seller_id', $userId)->count();
                $cancel = $this->model->where('status', '=', 6)->where('seller_id', $userId)->count();
                break;
            case 3:
                $prepare = $this->model->where('status', '=', 2)->where('trader_id', $userId)->count();
                $transportation = $this->model->where('status', '=', 3)->where('trader_id', $userId)->count();
                $received = $this->model->where('status', '=', 4)->where('trader_id', $userId)->count();
                $cancellation = $this->model->where('status', '=', 5)->where('trader_id', $userId)->count();
                $cancel = $this->model->where('status', '=', 6)->where('trader_id', $userId)->count();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return [
            'Chuẩn bị' => $prepare,
            'Vận chuyển' => $transportation,
            'Hoàn thành' => $received,
            'Hoàn trả' => $cancellation,
            'Bị hủy' => $cancel
        ];
    }
}

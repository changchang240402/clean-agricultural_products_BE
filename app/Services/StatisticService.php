<?php

namespace App\Services;

use App\Repositories\Item\ItemRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Review\ReviewRepository;
use App\Repositories\User\UserRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticService
{
    protected $userRepository;

    protected $orderRepository;

    protected $itemRepository;

    protected $reviewRepository;

    public function __construct(
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        ItemRepository $itemRepository,
        ReviewRepository $reviewRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->itemRepository = $itemRepository;
        $this->reviewRepository = $reviewRepository;
    }

    public function getStatisticByUserId(int $userId, int $role)
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $currentYear = $now->year;
        $currentMonth = $now->month;
        $total1 = $this->orderRepository->totalMoneyByUserId($userId, $role, $currentMonth, $currentYear);
        if (!$total1) {
            throw new Exception('Get total1 failed');
        }
        $total2 = $this->orderRepository->totalOrderByUserId($userId, $role, $currentMonth, $currentYear);
        if (!$total2) {
            throw new Exception('Get total2 failed');
        }
        if ($role === 0) {
            $total3 = $this->userRepository->totalUserByUserId($currentMonth, $currentYear);
            $total4 = $this->itemRepository->totalItemByUserId($currentMonth, $currentYear);
        } else {
            $total3 = $this->orderRepository->totalQuantityByUserId($userId, $role, $currentMonth, $currentYear);
            $total4 = $this->reviewRepository->totalReviewByUserId($userId, $role, $currentMonth, $currentYear);
        }
        if (!$total3) {
            throw new Exception('Get total3 failed');
        }
        if (!$total4) {
            throw new Exception('Get total3 failed');
        }
        $orderByMonth = $this->getStatisticsMoneyByMonnth($userId, $role, $currentMonth, $currentYear);
        if (!$orderByMonth) {
            throw new Exception('Get orderMonth failed');
        }
        $orderByStatus = $this->orderRepository->totalOrderByStatus($userId, $role);
        if (!$orderByStatus) {
            throw new Exception('Get orderMonth failed');
        }
        return [
            'total1' => $total1,
            'total2' => $total2,
            'total3' => $total3,
            'total4' => $total4,
            'order_month' => $orderByMonth,
            'order_status' => $orderByStatus
        ];
    }

    private function getStatisticsMoneyByMonnth($userId, $role, $currentMonth, $currentYear)
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = $currentMonth + $i;
            $year = $currentYear - 1;
            if ($month > 12) {
                $month = $month % 12;
                $year += floor(($currentMonth + $i - 1) / 12);
            }
            $months[] = [
                'month' => $month,
                'year' => $year,
            ];
        }
        $data = [];
        foreach ($months as $month) {
            $data[] = $this->orderRepository->totalMoneyByMonth($userId, $role, $month['month'], $month['year']);
        }
        return $data;
    }
}

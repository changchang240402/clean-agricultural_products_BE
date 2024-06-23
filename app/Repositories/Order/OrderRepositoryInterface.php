<?php

namespace App\Repositories\Order;

use App\Repositories\RepositoryInterface;

interface OrderRepositoryInterface extends RepositoryInterface
{
    public function getOrdersByUser();

    public function totalOrderDetailByUser();

    public function getOrderBill();

    public function statisticsOrder($userId, $role);

    public function getOrder($userId, $role);

    public function orderById($id, $userId);

    public function totalMoneyByUserId($userId, $role, $month, $year);

    public function totalOrderByUserId($userId, $role, $month, $year);

    public function totalQuantityByUserId($userId, $role, $month, $year);

    public function totalMoneyByMonth($userId, $role, $month, $year);

    public function totalOrderByStatus($userId, $role);
}

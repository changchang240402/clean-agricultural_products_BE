<?php

namespace App\Repositories\OrderDetail;

use App\Models\OrderDetail;
use App\Repositories\BaseRepository;
use App\Repositories\OrderDetail\OrderDetailRepositoryInterface;
use Exception;

class OrderDetailRepository extends BaseRepository implements OrderDetailRepositoryInterface
{
    public function getModel()
    {
        return OrderDetail::class;
    }
    public function getOrderDetailIdByUser($id)
    {
        return $this->model->where('order_id', $id)->get('id');
    }

    public function deleteMultipleOrderDetail(array $orderDetails)
    {
        $this->model->whereIn('id', $orderDetails)->delete();
        return true;
    }
}

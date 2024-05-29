<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderDetail\CreateOrderDetailRequest;
use App\Http\Requests\OrderDetail\UpdateOrderDetailRequest;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use App\Services\OrderDetailService;

class OrderDetailController extends Controller
{
    protected $orderDetailService;

    public function __construct(
        OrderDetailService $orderDetailService,
    ) {
        $this->orderDetailService = $orderDetailService;
    }
    public function createOrderDetailsByUser(CreateOrderDetailRequest $request)
    {
        $validated = $request->validated();
        try {
            $orderDetail = $this->orderDetailService->createOrderDetailsByUser($validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'orderDetail' => $orderDetail,
        ], 200);
    }

    public function updateOrderDetailsByUser($id, UpdateOrderDetailRequest $request)
    {
        $validated = $request->validated();
        try {
            $orderDetail = $this->orderDetailService->updateOrderDetailsByUser($id, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'orderDetail' => $orderDetail,
        ], 200);
    }

    public function deleteOrderDetailsByUser($id)
    {
        try {
            $orderDetail = $this->orderDetailService->deleteOrderDetailsByUser($id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'orderDetail' => $orderDetail,
            'delete' => 'Success',
        ], 200);
    }
}

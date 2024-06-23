<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\FilterProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\ProductService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productService;

    protected $notificationService;

    public function __construct(
        ProductService $productService,
        NotificationService $notificationService,
    ) {
        $this->productService = $productService;
        $this->notificationService = $notificationService;
    }

    public function getProductsByProductTypeId($id)
    {
        try {
            $product = $this->productService->getProductsByProductTypeId($id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ], 200);
    }

    public function getProduct(FilterProductRequest $request)
    {
        $page = request()->get('page', 1);
        $validated = $request->validated();
        try {
            $product = $this->productService->filterProduct($page, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ], 200);
    }

    public function listProduct()
    {
        try {
            $product = Product::all();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
        ], 200);
    }

    public function updateProduct($id, UpdateProductRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $update = $this->productService->updateProduct($id, $validated);
            $notificationData = [
                'notification_type_id' => 1,
                'target_type' => 0,
                'target_id' => null,
                'title' => 'Thông báo gía nông sản thị trường của ' . $validated['product_name'] . '.',
                'describe' => 'Chúng tôi vừa câp nhâp giá thị trường của nông sản ' . $validated['product_name'] . ' ngày hôm nay',
                'link' => '/seller/product',
            ];
            $this->notificationService->createNotification($notificationData);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Update successfully!',
                'data' => $update,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(
        ProductService $productService,
    ) {
        $this->productService = $productService;
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
}

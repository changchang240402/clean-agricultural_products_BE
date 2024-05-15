<?php

namespace App\Services;

use App\Repositories\Product\ProductRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected $productRepository;

    public function __construct(
        ProductRepository $productRepository,
    ) {
        $this->productRepository = $productRepository;
    }

    public function getProductsByProductTypeId($productTypeId)
    {
        return $this->productRepository->getProductsByProductTypeId($productTypeId);
    }
}

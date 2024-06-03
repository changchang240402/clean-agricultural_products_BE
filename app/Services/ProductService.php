<?php

namespace App\Services;

use App\Repositories\Product\ProductRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private const PAGINATE_PER_PAGE = 10;
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

    public function getProduct(
        int $page,
        int $productTypeId = null,
        string $sort = null,
    ) {
        $products = $this->productRepository->getProduct();
        if ($products->count() > 0) {
            if ($productTypeId) {
                $products = $this->filterByProductType($products, $productTypeId);
            }
            if ($sort) {
                $products = $this->filterBySort($products, $sort);
            }
            if ($products->isEmpty()) {
                throw new Exception('Product not found');
            }
            $perPage = self::PAGINATE_PER_PAGE;
            $productsPerPage = $products->forPage($page, $perPage);
            $paginatedProducts = new LengthAwarePaginator(
                $productsPerPage->values()->all(),
                $products->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return $paginatedProducts;
        }
    }
    private function filterBySort($products, $sort)
    {
        if ($sort === 'asc') {
            return $products->sortBy('total_items');
        } elseif ($sort === 'desc') {
            return $products->sortByDesc('total_items');
        }
    }

    private function filterByProductType($products, $productTypeId)
    {
        return $productTypeId ? $products->filter(function ($product) use ($productTypeId) {
            if ($product['product_type_id'] === $productTypeId) {
                return true;
            }
            return false;
        }) : $products;
    }
    public function filterProduct(int $page, array $filter)
    {
        $productTypeId = null;
        $sort = null;
        if (isset($filter['product_type_id'])) {
            $productTypeId = $filter['product_type_id'];
        }
        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
        }
        return $this->getProduct($page, $productTypeId, $sort);
    }
}

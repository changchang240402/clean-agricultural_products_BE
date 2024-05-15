<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Exception;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getModel()
    {
        return Product::class;
    }

    public function getProductsByProductTypeId($productTypeId)
    {
        return $this->model
            ->where('product_type_id', $productTypeId)
            ->select('id', 'product_name', 'product_type_id', 'price_max', 'price_min')
            ->withCount([
                'items as total_items' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['in use']);
                }
            ])
            ->get();
    }
}

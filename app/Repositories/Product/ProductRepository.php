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

    public function getProduct()
    {
        $userId = auth()->id();
        $user = auth()->user();
        $products = [];
        switch ($user->role) {
            case 0:
                $products = $this->model->withCount('items as total_items')->orderBy('updated_at', 'desc')->get();
                break;
            case 2:
                $products = $this->model
                ->withCount([
                    'items as total_items' => function ($query) use ($userId) {
                        $query->where('seller_id', '=', $userId);
                    }
                ])->orderBy('updated_at', 'desc')->get();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return $products;
    }
}

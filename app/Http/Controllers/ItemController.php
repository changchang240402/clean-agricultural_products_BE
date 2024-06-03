<?php

namespace App\Http\Controllers;

use App\Http\Requests\Item\FilterItemAdminAndShopRequest;
use App\Http\Requests\Item\FilterItemRequest;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $itemService;

    public function __construct(
        ItemService $itemService,
    ) {
        $this->itemService = $itemService;
    }

    public function getItemsToUser(FilterItemRequest $request)
    {
        $page = request()->get('page', 1);
        $validated = $request->validated();
        try {
            $item = $this->itemService->filterItem($page, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function getItems(FilterItemAdminAndShopRequest $request)
    {
        $page = request()->get('page', 1);
        $validated = $request->validated();
        try {
            $item = $this->itemService->filterItems($page, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function getTopItemSale()
    {
        try {
            $item = $this->itemService->getTopItemSale();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function getNewItemSale()
    {
        try {
            $item = $this->itemService->getNewItemSale();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function itemDetail($id)
    {
        try {
            $item = $this->itemService->itemDetail($id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }

    public function getItemByShop($id)
    {
        try {
            $item = $this->itemService->getItemByShop($id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ], 200);
    }
    // public function updateStatusFrom3To4()
    // {
    //     $roles = [50, 60, 70, 80, 90, 100];
    //     $products = Item::all();

    //     foreach ($products as $product) {
    //         $print = $product->price;
    //         $role = array_rand($roles);
    //         $product->type = $roles[$role];
    //         $product->price_type = $roles[$role] * $print;
    //         $product->save();
    //     }
    // }
}

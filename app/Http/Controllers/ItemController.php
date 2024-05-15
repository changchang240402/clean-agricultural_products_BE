<?php

namespace App\Http\Controllers;

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
    // public function updateStatusFrom3To4()
    // {
    //     $role = [1,3];
    //     $products = Item::where('status', 4)->get();

    //     foreach ($products as $product) {
    //         $product->status = 0;
    //         $product->save();
    //     }
    // }
}

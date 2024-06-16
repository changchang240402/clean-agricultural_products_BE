<?php

namespace App\Http\Controllers;

use App\Http\Requests\Item\FilterItemAdminAndShopRequest;
use App\Http\Requests\Item\FilterItemRequest;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

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

    public function getItemWarning()
    {
        $notifications = [];
        try {
            $items = $this->itemService->getItemWarning();
            if ($items->isEmpty()) {
                return response()->json(['message' => 'Không có sản phẩm nào cần cảnh báo'], 422);
            }
            foreach ($items as $item) {
                $url = encodeId($item['id']);
                $notificationData = [
                    'notification_type_id' => 2,
                    'title' => 'Thông báo nhắc nhở giá sản phẩm nằm ngoài so hơn thị trường.',
                    'target_type' => $item['id'],
                    'target_id' => $item['seller_id'],
                    'describe' => 'Bạn có một sản phẩm thuộc loại' .  $item['product_name'] .  ' nằm ngoài so hơn thị trường giá hiện tại. Vui lòng kiểm tra và cân nhắc cập nhập lại giá',
                    'link' => '/seller/item/' . $url,
                ];
                $notifications[] = $notificationData;
            }
            $noti = Notification::insert($notifications);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'noti' => $noti,
            'item' => $items,
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

    public function getItemBan()
    {
        try {
            $items = $this->itemService->getItemBan();
            if ($items->isNotEmpty()) {
                Item::whereIn('id', $items)->update(['status' => config('constants.STATUS_ITEM')['archived']]);
            } else {
                return response()->json(['message' => 'Không có sản phẩm phải khóa'], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'item' => $items,
        ], 200);
    }

    public function getItemUnban()
    {
        DB::beginTransaction();
        try {
            $items = $this->itemService->getItemUnBan();
            if ($items->isNotEmpty()) {
                Item::whereIn('id', $items)->update(['status' => config('constants.STATUS_ITEM')['in use']]);
                Notification::whereIN('target_type', $items)->delete();
            } else {
                return response()->json(['message' => 'Không có sản phẩm'], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        DB::commit();
        return response()->json([
            'success' => true,
            'item' => $items,
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

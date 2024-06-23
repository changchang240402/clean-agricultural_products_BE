<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\FilterOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Item;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\MailService;
use App\Services\MapboxService;
use App\Services\NotificationService;
use App\Services\OrderDetailService;
use App\Services\OrderService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    protected $orderService;
    protected $mapboxService;
    protected $userService;
    protected $mailService;
    protected $orderDetailService;

    protected $notificationService;

    public function __construct(
        OrderService $orderService,
        MapboxService $mapboxService,
        UserService $userService,
        OrderDetailService $orderDetailService,
        MailService $mailService,
        NotificationService $notificationService,
    ) {
        $this->orderService = $orderService;
        $this->mapboxService = $mapboxService;
        $this->userService = $userService;
        $this->orderDetailService = $orderDetailService;
        $this->mailService = $mailService;
        $this->notificationService = $notificationService;
    }

    public function getOrdersByUser()
    {
        try {
            $order = $this->orderService->getOrdersByUser();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ], 200);
    }

    public function getTotalOrder()
    {

        try {
            $order = $this->orderService->totalOrderDetailByUser();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ], 200);
    }
    public function deleteOrderByUser($id)
    {
        try {
            $order = $this->orderService->deleteOrderByUser($id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'delete' => 'Success',
        ], 200);
    }

    public function orderList(FilterOrderRequest $request)
    {
        $userId = auth()->id();
        $user = auth()->user();
        $page = request()->get('page', 1);
        $validated = $request->validated();
        try {
            $orders = $this->orderService->filterOrder($userId, $user->role, $page, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order' => $orders,
        ], 200);
    }

    public function statisticsOrder()
    {
        $userId = auth()->id();
        $user = auth()->user();
        try {
            $statisticsOrder = $this->orderService->statisticsOrder($userId, $user->role);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'statisticsOrder' => $statisticsOrder,
        ], 200);
    }

    public function orderbyId($id)
    {
        $userId = auth()->id();
        $user = auth()->user();
        try {
            $orderbyId = $this->orderService->orderById($id, $userId);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'order' => $orderbyId,
            'role' => $user->role
        ], 200);
    }


    public function updateBill()
    {
        DB::beginTransaction();
        try {
            $orders = $this->orderService->updateBill();
            $test = [];
            $notificationData = [];
            foreach ($orders as $order) {
                $resul = $this->userService->findTraderShip($order->seller->address, $order->total_quantity);
                $test[] = $resul->id;
                $order->trader_id = $resul->id;
                $order->status = 2;
                $order->order_date = Carbon::now('Asia/Ho_Chi_Minh');
                $order->save();
                $code = 12040900 + $order->id;
                $url = encodeId($order->id);
                $notificationData[] = [
                    'notification_type_id' => 7,
                    'target_type' => 0,
                    'target_id' => $order->seller_id,
                    'title' => 'Thông báo có đơn hàng #MDH' . $code . ' được đặt',
                    'describe' => 'Bạn có một đơn hàng #MDH' . $code .  ' được đặt. Vui lòng kiểm tra và chuẩn bị hàng để giao.',
                    'link' => '/seller/tracking/' . $url,
                    'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                    'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                ];
                $notificationData[] = [
                    'notification_type_id' => 8,
                    'target_type' => 0,
                    'target_id' => $resul->id,
                    'title' => 'Thông báo có đơn hàng #MDH' . $code . ' cần vận chuyển',
                    'describe' => 'Bạn có một đơn hàng #MDH' . $code .  ' cần vận chuyển. Vui lòng kiểm tra và đến nơi nhận hàng',
                    'link' => '/trader/tracking/' . $url,
                    'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                    'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                ];

                $this->mailService->sendMail($order->seller->email, $notificationData[0]['title'], $notificationData[0], null, null);
                $this->mailService->sendMail($resul->email, $notificationData[1]['title'], $notificationData[1], null, null);
            }
            Notification::insert($notificationData);

            // Remove duplicates and update traders
            $uniqueTest = array_unique($test);
            User::whereIn('id', $uniqueTest)
                ->where('role', config('constants.ROLE')['trader'])
                ->update(['status' => 3]);
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
            'order' => $uniqueTest,
            'ab' => $orders,
        ], 200);
    }

    public function updateBillById($id)
    {
        $userId = auth()->id();
        DB::beginTransaction();
        try {
            $order = $this->orderService->updateBillById($userId, $id);
            $resul = $this->userService->findTraderShip($order->seller->address, $order->total_quantity);
            $order->trader_id = $resul->id;
            $order->save();
            $code = 12040900 + $id;
            $url = encodeId($order->id);
            $link = '/trader/tracking/' . $url;
            Notification::where('target_id', $userId)->where('link', $link)->delete();
            $notificationData = [
                'notification_type_id' => 8,
                'target_type' => 0,
                'target_id' => $resul->id,
                'title' => 'Thông báo có đơn hàng #MDH' . $code . ' cần vận chuyển',
                'describe' => 'Bạn có một đơn hàng #MDH' . $code .  ' cần vận chuyển. Vui lòng kiểm tra và đến nơi nhận hàng',
                'link' => $link,
                'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
            ];
            $this->notificationService->createNotification($notificationData);
            $this->mailService->sendMail($resul->email, $notificationData['title'], $notificationData, null, null);
            $user = User::where('id', $resul->id)
                ->where('role', config('constants.ROLE')['trader'])
                ->update(['status' => 3]);
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
            'order' => $order,
            'user' => $user,
        ], 200);
    }

    public function updateOrder($id, UpdateOrderRequest $request)
    {
        $userId = auth()->id();
        $validated = $request->validated();
        $notificationData = [];
        $code = 12040900 + $id;
        $url = encodeId($id);
        switch ($validated['time']) {
            case 1:
                try {
                    $order = Order::where('id', '=', $id)->where('user_id', '=', $userId)->first();
                    $order->update(['status' => 6, 'cancellation_note' => $validated['content'], 'order_cancellation_date' => Carbon::now('Asia/Ho_Chi_Minh')]);
                    $notificationData[] = [
                        'notification_type_id' => 12,
                        'target_type' => 0,
                        'target_id' => $order->seller_id,
                        'title' => 'Thông báo có đơn hàng #MDH' . $code . ' đã bị hủy bởi người mua.',
                        'describe' => 'Lý do hủy đơn hàng là ' . $validated['content'],
                        'link' => '/seller/tracking/' . $url,
                        'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                        'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                    ];
                    $notificationData[] = [
                        'notification_type_id' => 12,
                        'target_type' => 0,
                        'target_id' => $order->trader_id,
                        'title' => 'Thông báo có đơn hàng #MDH' . $code . ' cần vận chuyển đã bị hủy bởi người mua.',
                        'describe' => 'Lý do hủy đơn hàng là ' . $validated['content'],
                        'link' => '/trader/tracking/' . $url,
                        'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                        'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                    ];
                } catch (\Throwable $th) {
                    return response()->json([
                        'success' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
                break;
            case 2:
                try {
                    $order = Order::where('id', '=', $id)->where('seller_id', '=', $userId)->first();
                    $order->update(['status' => 3, 'delivery_date' => Carbon::now('Asia/Ho_Chi_Minh')]);
                    $notificationData[] = [
                        'notification_type_id' => 9,
                        'target_type' => 0,
                        'target_id' => $order->user_id,
                        'title' => 'Thông báo có đơn hàng #MDH' . $code . ' đang vận chuyển đến bạn.',
                        'describe' => 'Đơn hàng đã xuất kho và đang vận chuyển đến bạn.',
                        'link' => '/user/tracking/' . $url,
                        'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                        'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                    ];
                } catch (\Throwable $th) {
                    return response()->json([
                        'success' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
                break;
            case 3:
                try {
                    $order = Order::where('id', '=', $id)->where('trader_id', '=', $userId)->first();
                    $order->update(['status' => 4, 'received_date' => Carbon::now('Asia/Ho_Chi_Minh')]);
                    $notificationData[] = [
                        'notification_type_id' => 10,
                        'target_type' => 0,
                        'target_id' => $order->seller_id,
                        'title' => 'Thông báo có đơn hàng #MDH' . $code . ' đã vận chuyển đến người mua.',
                        'describe' => 'Đơn hàng đã được vận chuyển đến người mua.',
                        'link' => '/seller/tracking/' . $url,
                        'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                        'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                    ];
                } catch (\Throwable $th) {
                    return response()->json([
                        'success' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
                break;
            case 4:
                try {
                    $order = Order::where('id', '=', $id)->where('trader_id', '=', $userId)->first();
                    $order->update(['status' => 5, 'received_date' => Carbon::now('Asia/Ho_Chi_Minh'), 'cancellation_note' => $validated['content'],]);
                    $notificationData[] = [
                        'notification_type_id' => 11,
                        'target_type' => 0,
                        'target_id' => $order->seller_id,
                        'title' => 'Thông báo có đơn hàng #MDH' . $code . ' bị hoàn trả.',
                        'describe' => 'Lý do hủy đơn hàng là ' . $validated['content'],
                        'link' => '/seller/tracking/' . $url,
                        'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
                        'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')
                    ];
                } catch (\Throwable $th) {
                    return response()->json([
                        'success' => false,
                        'message' => $th->getMessage()
                    ], 500);
                }
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        Notification::insert($notificationData);
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function updateStatusFrom3To4()
    {
        // $addess = 'Đường số 7, Phường Linh Trung (Quận Thủ Đức cũ), Thành phố Thủ Đức, Tp Hồ Chí Minh';
        // $total = "360.00";
        // $test = $this->userService->findTraderShip($addess, $total);
        // $products = Order::all();

        // $cost = $this->userService->getCostShip($product->user_id, $product->seller_id);
        // $calculateCost = $this->orderDetailService->calculateCost($cost);
        // $price = 0;
        // $type = 0;

        // $orders = Order::whereNotNull('order_cancellation_date')->update(['status' => 6]);
        // $emails = User::where('role', '=', config('constants.ROLE')['seller'])
        //     ->where('status', '=', config('constants.STATUS_USER')['in use'])
        //     ->get('email');
        // $email = $emails[0];
        // $bcc = array_slice($emails, 1);
        // $product->cost = $calculateCost;
        // $product->shipping_money = $product->cost * $product->total_quantity;
        // $product->save();
        try {
            $notifis = Notification::whereNull('created_at')->update(['created_at' => Carbon::now('Asia/Ho_Chi_Minh'), 'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')]);
            // foreach ($notifis as $notifi) {
            //     // $randomTimestamp = now()->subDays(rand(0, 30))->setTime(rand(0, 23), rand(0, 59), rand(0, 59));
            //     $notifi->created_at = Carbon::now('Asia/Ho_Chi_Minh');
            //     $notifi->updated_at = Carbon::now('Asia/Ho_Chi_Minh');
            //     $notifi->save();
            // }
            // $url = encodeId(2);
            // $data1 = [
            //     'notification_type_id' => 7,
            //     'target_type' => 0,
            //     'target_id' => 1,
            //     'title' => 'Thông báo có đơn hàng #MDH273 được đặt',
            //     'describe' => 'Bạn có một đơn hàng #MDH273 được đặt. Vui lòng kiểm tra và chuẩn bị hàng để giao.',
            //     'link' => '/seller/tracking/' . $url,
            // ];
            // $email = 'thuytrangdao2404@gmail.com';
            // $this->mailService->sendMail($email, $data1['title'], $data1);
            // $now = Carbon::now('Asia/Ho_Chi_Minh');
            // $year = $now->year;
            // $month = $now->month;
            // $money = Order::where('status', '!=', 1)->sum('total_price');
            // $money1 = Order::where('status', '!=', 1)->sum('shipping_money');
            // $price_now = Order::where('status', '!=', 1)
            //     ->whereYear('order_date', $year)
            //     ->whereMonth('order_date', $month)->sum('total_price');
            // $shipping_now = Order::where('status', '!=', 1)
            //     ->whereYear('order_date', $year)
            //     ->whereMonth('order_date', $month)
            //     ->sum('shipping_money');
            // $money_now = $price_now + $shipping_now;
            // $item = Item::where('seller_id', 2)->pluck('id')->toArray();
            // $review_item = Review::where('review_type', '=', config('constants.REVIEW')['item'])
            //     ->whereIn('review_id', $item)
            //     ->count();
            // $this->mailService->sendIssueUpdate();
            // $data = Order::where('id', 1)->first();
            // $data['url'] = '/user/tracking/';
            // $products = Product::all()->keyBy('product_name');
            return response()->json([
                'success' => true,
                'message' => 'Status updated and email sent successfully.',
                'data' => $notifis,
                // 'data1' => count($emails),
                // 'data2' => $products,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

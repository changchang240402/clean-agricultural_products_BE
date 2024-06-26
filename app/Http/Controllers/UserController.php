<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStatus;
use App\Http\Requests\User\FilterAdminRequest;
use App\Http\Requests\User\FilterUserRequest;
use App\Http\Requests\User\UploadExcel;
use App\Services\StatisticService;
use App\Services\NotificationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\MailService;

class UserController extends Controller
{
    protected $userService;

    protected $statisticService;

    protected $notificationService;

    protected $mailService;

    public function __construct(
        UserService $userService,
        StatisticService $statisticService,
        NotificationService $notificationService,
        MailService $mailService,
    ) {
        $this->userService = $userService;
        $this->statisticService = $statisticService;
        $this->notificationService = $notificationService;
        $this->mailService = $mailService;
    }

    public function getShopsByUserId(FilterUserRequest $request)
    {
        $validated = $request->validated();
        try {
            $user = $this->userService->filterShop($validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'shop' => $user,
        ], 200);
    }

    public function sellerDetailById($id)
    {
        try {
            $seller = $this->userService->sellerDetailById($id);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'shop' => $seller,
        ], 200);
    }

    public function getSellerToAdmin(FilterAdminRequest $request)
    {
        $page = request()->get('page', 1);
        $validated = $request->validated();
        try {
            $user = $this->userService->filterSeller($page, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'shop' => $user,
        ], 200);
    }

    public function getUserToAdmin(FilterAdminRequest $request)
    {
        $page = request()->get('page', 1);
        $validated = $request->validated();
        try {
            $user = $this->userService->filterUser($page, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
        ], 200);
    }

    public function getTraderToAdmin(FilterAdminRequest $request)
    {
        $page = request()->get('page', 1);
        $validated = $request->validated();
        try {
            $trader = $this->userService->filterTrader($page, $validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'trader' => $trader,
        ], 200);
    }

    public function statistic()
    {
        $userId = auth()->id();
        $user = auth()->user();
        try {
            $statistic = $this->statisticService->getStatisticByUserId($userId, $user->role);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'statistic' => $statistic,
        ], 200);
    }

    public function upload(UploadExcel $request)
    {
        $path = $request->file('file');
        $address = "execls/";
        DB::beginTransaction();
        try {
            $fileUrl = $this->userService->uploadS3($path, $address);
            if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                throw new \Exception("Invalid file URL.");
            }
            $contents = file_get_contents($fileUrl);
            if ($contents === false) {
                throw new \Exception("Failed to read file contents.");
            }
            $tempPath = tempnam(sys_get_temp_dir(), 'excel') . '.xlsx';
            file_put_contents($tempPath, $contents);
            $importedData = Excel::toCollection([], $tempPath);
            unlink($tempPath);
            if ($importedData->isNotEmpty()) {
                $importedData->first()->splice(0, 1);
                // $products = Product::all()->keyBy('product_name');
                $updateData = [];
                foreach ($importedData[0] as $row) {
                    $product = Product::where('product_name', '=', $row[0])->first();
                    if ($product) {
                        $product->price_max = $row[1] * 1000 + 5000;
                        $product->price_min = $row[1] * 1000 - 5000;
                        $product->save();
                        $updateData[] = $product;
                    }
                }
            } else {
                return response()->json(['message' => 'No data found in Excel file'], 422);
            }
            $notificationData = [
                'notification_type_id' => 1,
                'target_type' => 0,
                'target_id' => null,
                'title' => 'Thông báo gía nông sản thị trường.',
                'describe' => 'Chúng tôi vừa câp nhâp giá thị trường của nông sản ngày hôm nay',
                'link' => '/seller/product',
            ];
            $this->notificationService->createNotification($notificationData);
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $updateData,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function updateStatusUser($id, UpdateStatus $request)
    {
        $validated = $request->validated();
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'No data found'], 422);
            }
            switch ($validated['time']) {
                case 1:
                    $user->status = $validated['status'];
                    $user->save();
                    break;
                case 2:
                    $user->status = $validated['status'];
                    $user->save();
                    $notificationData = [
                        'title' => 'Thông báo về tài khoản đăng ký của bạn.',
                        'describe' => 'Chúng tôi đã kiểm tra và phê duyệt thông tin đăng ký của bạn. Xin vui lòng quay lại đăng nhập để trải nghiệp hệ thống của chúng tôi',
                        'link' => '',
                    ];
                    $this->mailService->sendMail($user->email, $notificationData['title'], $notificationData, null, null);
                    break;
                case 3:
                    $notificationData = [
                        'title' => 'Thông báo về tài khoản đăng ký của bạn.',
                        'describe' => 'Chúng tôi đã kiểm tra thông tin đăng ký của bạn và thấy thông tin của bạn chưa chính xác. Xin vui lòng quay lại đăng ký để tạo tài khoản khác có thông tin xác thực hơn. Xin cảm ơn.',
                        'link' => '/register',
                    ];
                    $this->mailService->sendMail($user->email, $notificationData['title'], $notificationData, null, null);
                    $user->delete();
                    break;
                default:
                    return response()->json(['message' => 'Unknown Role'], 400);
            }
            return response()->json(['message' => 'Cập nhập thành công'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}

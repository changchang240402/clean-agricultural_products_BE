<?php

namespace App\Http\Controllers;

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
            $tempPath = tempnam(sys_get_temp_dir(), 'excel');
            file_put_contents($tempPath, $contents);
            $importedData = Excel::toCollection([], $tempPath);
            unlink($tempPath);
            if ($importedData->isNotEmpty()) {
                $importedData->first()->splice(0, 1);
                $products = Product::all()->keyBy('product_name');
                $updateData = [];
                foreach ($importedData[0] as $row) {
                    $productName = $row[0];
                    if (isset($products[$productName])) {
                        $updateData[] = [
                            'product_name' => $productName,
                            'price_max' => $row[1] * 1000 + 5000,
                            'price_min' => $row[1] * 1000 - 5000,
                            'product_type_id' => $products[$productName]->product_type_id,
                        ];
                    }
                }
                if (!empty($updateData)) {
                    Product::upsert($updateData, ['product_name'], ['price_max', 'price_min', 'product_type_id']);
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
            $emails = User::where('role', '=', config('constants.ROLE')['seller'])
                ->where('status', '=', config('constants.STATUS_USER')['in use'])
                ->pluck('email')
                ->all();
            if (count($emails) === 0) {
                return response()->json(['message' => 'No emails found'], 422);
            }
            $email = $emails[0];
            $bcc = array_slice($emails, 1);
            $this->mailService->sendMail($email, $notificationData['title'], $notificationData, null, $bcc);
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
}

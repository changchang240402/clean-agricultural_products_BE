<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\FilterAdminRequest;
use App\Http\Requests\User\FilterUserRequest;
use App\Services\StatisticService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    protected $statisticService;

    public function __construct(
        UserService $userService,
        StatisticService $statisticService,
    ) {
        $this->userService = $userService;
        $this->statisticService = $statisticService;
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
}

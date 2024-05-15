<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\FilterUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(
        UserService $userService,
    ) {
        $this->userService = $userService;
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

    public function getSellerToAdmin()
    {
        try {
            $user = $this->userService->getSellerToAdmin();
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
}

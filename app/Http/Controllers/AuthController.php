<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        try {
            $create = $this->authRepository->register($validated);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Create successfully!',
            'data' => $create
        ], 200);
    }
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = $this->authRepository->findUserByEmail($validated['email']);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email does not exist in the system'
                ], 401);
            }

            $this->authRepository->checkUserStatus($user);

            $token = $this->authRepository->createAccesstoken($validated);
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password incorrect'
                ], 401);
            }

            $refreshToken = $this->authRepository->createRefreshToken($user);

            $response =  $this->authRepository->login($token, $refreshToken);
            return response()->json(
                $response,
                200
            );
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], $th->getCode());
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->authRepository->logout();
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 401);
        }
    }
}

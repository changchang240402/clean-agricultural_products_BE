<?php

namespace App\Repositories\Auth;

interface AuthRepositoryInterface
{
    public function findUserByEmail($email);

    public function createAccesstoken($validated);

    public function createRefreshToken($user);

    public function login($token, $refreshToken);

    public function register($user);

    public function logout();

    public function checkUserStatus($user);

    public function getUserProfile($userId);
}

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post("login", [AuthController::class, "login"])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    ;
});

Route::group([
    'middleware' => ['checkLogin'],
    'prefix' => 'auth'
], function () {
    Route::post("logout", [AuthController::class, "logout"])->name('logout');
    Route::get("me", [AuthController::class, "getUserProfile"]);
    Route::get("products/{id}", [ProductController::class, "getProductsByProductTypeId"]);
});

Route::group([
    'middleware' => ['auth.user'],
    'prefix' => 'user'
], function () {
    Route::get("shops", [UserController::class, "getShopsByUserId"]);
    Route::get("items", [ItemController::class, "getItemsToUser"]);
    Route::get("topItems", [ItemController::class, "getTopItemSale"]);
    Route::get("newItems", [ItemController::class, "getNewItemSale"]);
});

Route::group([
    'middleware' => ['auth.admin'],
    'prefix' => 'admin'
], function () {
    Route::get("sellers", [UserController::class, "getSellerToAdmin"]);
});

// Route::get("abc", [ItemController::class, "updateStatusFrom3To4"]);

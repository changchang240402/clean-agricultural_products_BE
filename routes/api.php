<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VnPayController;
use App\Models\Review;
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
    Route::get("item/{id}", [ItemController::class, "itemDetail"]);
    Route::get("reviews", [ReviewController::class, "getReviewsToId"]);
    Route::get("shop/{id}", [UserController::class, "sellerDetailById"]);
    Route::get("orderList", [OrderController::class, "orderList"]);
    Route::get("statisticsOrder", [OrderController::class, "statisticsOrder"]);
    Route::get("order/{id}", [OrderController::class, "orderbyId"]);
    Route::get("products", [ProductController::class, "getProduct"]);
    Route::get("items", [ItemController::class, "getItems"]);
    Route::get("statistic", [UserController::class, "statistic"]);
});

Route::group([
    'middleware' => ['auth.user'],
    'prefix' => 'user'
], function () {
    Route::get("shops", [UserController::class, "getShopsByUserId"]);
    Route::get("items", [ItemController::class, "getItemsToUser"]);
    Route::get("topItems", [ItemController::class, "getTopItemSale"]);
    Route::get("newItems", [ItemController::class, "getNewItemSale"]);
    Route::get("itemShop/{id}", [ItemController::class, "getItemByShop"]);
    Route::get("orders", [OrderController::class, "getOrdersByUser"]);
    Route::get("total", [OrderController::class, "getTotalOrder"]);
    Route::post("updateBill", [OrderController::class, "updateBill"]);
    Route::post("orderDetail", [OrderDetailController::class, "createOrderDetailsByUser"]);
    Route::post("orderDetail/{id}", [OrderDetailController::class, "updateOrderDetailsByUser"]);
    Route::delete("orderDetail/{id}", [OrderDetailController::class, "deleteOrderDetailsByUser"]);
    Route::delete("order/{id}", [OrderController::class, "deleteOrderByUser"]);
    Route::post('/create_payment_url', [VnPayController::class, 'createPaymentUrl']);
    Route::get('/vnpay_return', [VnPayController::class, 'vnPayReturn']);
    Route::post('/create-payment-intent', [StripeController::class, 'createPaymentIntent']);
});

Route::group([
    'middleware' => ['auth.admin'],
    'prefix' => 'admin'
], function () {
    Route::get("sellers", [UserController::class, "getSellerToAdmin"]);
    Route::get("users", [UserController::class, "getUserToAdmin"]);
    Route::get("traders", [UserController::class, "getTraderToAdmin"]);
});

Route::get("abc", [OrderController::class, "updateStatusFrom3To4"]);
Route::get('/directions', [OrderController::class, 'getDirections']);

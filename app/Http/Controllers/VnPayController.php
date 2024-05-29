<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnPayController extends Controller
{
    protected $vnp_TmnCode = 'SPTG4CYV';
    protected $vnp_HashSecret = 'IEHA82ER1ZS8J2Q14VRMN8HBZQT9561N';
    protected $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    protected $vnp_ReturnUrl = 'http://localhost:3000/user/vnpay_return';
    public function createPaymentUrl(Request $request)
    {
        $vnp_TmnCode = "SPTG4CYV"; // Website ID in VNPAY System
        $vnp_HashSecret = "IEHA82ER1ZS8J2Q14VRMN8HBZQT9561N"; // Secret key
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "https://flowbite.com/blocks/e-commerce/order-tracking/";

        $vnp_TxnRef = 1; // Order ID
        $vnp_OrderInfo = "Noi dung thanh toan";
        $vnp_OrderType = 'billpayment'; // Ensure the correct case
        $vnp_Amount = 1000 * 100;
        $vnp_BankCode = 'NCB';
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_Locale" => 'vn',
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_BankCode" => $vnp_BankCode,
            "vnp_IpAddr" => '127.0.0.1',
            "vnp_ExpireDate" => $vnp_ExpireDate,
        );

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $hashdata = ltrim($hashdata, '&');
        $query = rtrim($query, '&');

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

        return response()->json([
            'code' => '00',
            'message' => 'success',
            'data' => $vnp_Url
        ]);
    }
    public function vnPayReturn(Request $request)
    {
        $vnp_SecureHash = $request->query('vnp_SecureHash');
        $inputData = $request->query();
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            return response()->json(['message' => 'success', 'data' => $inputData]);
        } else {
            return response()->json(['message' => 'fail']);
        }
    }
}

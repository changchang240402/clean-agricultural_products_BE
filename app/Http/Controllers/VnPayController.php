<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VnPayController extends Controller
{
    protected $vnp_TmnCode = "LJU98Q74"; //Website ID in VNPAY System
    protected $vnp_HashSecret = "CTC2U1KHPFSP70A5VR310TH2Y4PBF599"; //Secret key
    protected $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
    protected $vnp_Returnurl = "http://localhost:3000/user/vnpay_return";
    protected $vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";
    public function createPaymentUrl(Request $request)
    {
        $vnp_TxnRef = $request->input('vnp_TxnRef');
        $vnp_OrderInfo = "Thanh toan cho giao dich " . $request->input('vnp_TxnRef');
        $vnp_OrderType = "MDD" . $request->input('vnp_TxnRef');
        $vnp_Amount = $request->input('vnp_Amount') * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
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
            return response()->json([
                'success' => true,
                'data' => $inputData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Xảy ra lỗi không mong muốn'
            ], 401);
        }
    }
}

<?php

return [
    'ROLE' => [
        "admin" => 0,
        "user" => 1,
        "seller" => 2,
        "trader" => 3
    ],

    'PRODUCT_TYPES' => [
        'ngũ cốc',
        'rau củ',
        'trái cây',
        'thực phẩm công nghiệp',
        'khác'
    ],

    'STATUS_ITEM' => [
        "in use" => 0,
        "archived" => 1,
        "accept" => 2,
        "delete" => 3
    ],

    'STATUS_USER' => [
        "accept" => 0,
        "in use" => 1,
        "archived" => 2,
        "shipping" => 3,
    ],

    'ORDER_TYPES' => [
        'Chưa đặt đơn hàng.',
        'User đặt đơn hàng.',
        'Seller nhận đơn hàng đặt.',
        'Seller không nhận đơn hàng đặt.',
        'Trader nhận vận chuyển.',
        'Seller đã bàn giao đơn hàng cho Trader.',
        'Trader đang vận chuyển đơn hàng.',
        'Trader đã vận chuyển đơn hàng đến User.',
        'Trader xác nhận User không nhận đơn hàng.',
        'User xác nhận đơn hàng.',
        'User xác nhận không nhận đơn hàng đến.',
        'User đột ngột hủy đơn hàng.',
    ],

    'REVIEW_TYPES' => [
        'Chưa có đánh giá nào.',
        'User đánh giá mặt hàng.',
        'User đánh giá Trader.',
        'Seller đánh giá Trader.'
    ],

    'REVIEW' => [
        "user" => 0,
        "item" => 1
    ],

    'NUMBER_STAR' => [
        "1 sao" => 1,
        "2 sao" => 2,
        "3 sao" => 3,
        "4 sao" => 4,
        "5 sao" => 5
    ],

    'NOTIFICATION_TYPES' => [
        'Thông báo gía nông sản thị trường.',
        'Thông báo nhắc nhở giá sản phẩm cao hơn thị trường.',
        'Thông báo cảnh báo giá sản phẩm thấp hơn thị trường.',
        'Thông báo hệ thống tự ẩn sản phảm vì vượt quá số lần cảnh cáo về giá.',
        'Thông báo hệ thống đã gỡ lệnh ẩn sản phẩm.',
        'Thông báo hệ thống đã xác nhận sản phẩm user đăng lên.',
        'Thông báo User đặt đơn hàng đến Seller.',
        'Thông báo Seller xác nhận đơn hàng đặt đến User.',
        'Thông báo có đơn hàng cần vận chuyển đến Trader.',
        'Thông báo Seller không nhận đơn hàng đặt đến User kèm lí do.',
        'Thông báo Trader nhận vận chuyển đến Seller.',
        'Thông báo Seller đã bàn giao đơn hàng cho Trader đến User',
        'Thông báo Trader đang vận chuyển đơn hàng đến User.',
        'Thông báo Trader đã vận chuyển đơn hàng đến cho User đến Seller.',
        'Thông báo User không nhận đơn hàng từ Trader kèm lý do đến Seller.',
        'Thông báo User nhận đơn hàng thành công đến Seller.',
        'Thông báo User xác nhận không nhận đơn hàng đến Seller.',
        'Thông báo User đột ngột hủy đơn hàng đến Trader và Seller.',
    ],

    'VEHICLES' => [
        'Isuzu QKR230',
        'Isuzu QKR270',
        'Isuzu NMR310',
        'Isuzu NPR400',
        'Tata Super Ace',
        'Suzuki Carry Truck',
        'Suzuki Blind Van',
        'Suzuki Carry Pro',
        'Hyundai New Porter H150',
        'Hyundai New Mighty N250',
        'Hyundai New Mighty N250SL',
        'Hyundai New Mighty 75S',
        'Hyundai Mighty EX6',
    ],

    'PRODUCT' => [
        'Gạo Thơm' => 1,
        'Gạo Nếp' => 1,
        'Gạo Lứt' => 1,
        'Gạo ST25' => 1,
        'Gạo Nếp Cẩm' => 1,
        'Ngô Nếp' => 1,
        'Ngô' => 1,
        'Khoai Lang' => 1,
        'Khoai Môn' => 1,
        'Khoai Lang Tím' => 1,
        'Khoai Sọ' => 1,
        'Sắn Dây' => 1,
        'Đậu Xanh' => 1,
        'Đậu Đỏ' => 1,
        'Đậu Nành' => 1,
        'Đậu Hà Lan' => 1,
        'Khoai tây' => 2,
        'Cà rốt' => 2,
        'Củ cải trắng' => 2,
        'Củ cải đỏ' => 2,
        'Cải bắp' => 2,
        'Cải bẹ' => 2,
        'Cải bó xôi' => 2,
        'Cải cúc' => 2,
        'Cải dền' => 2,
        'Cải ngọt' => 2,
        'Cải thìa' => 2,
        'Cải xanh' => 2,
        'Cà chua' => 2,
        'Bí đỏ' => 2,
        'Hành tây' => 2,
        'Chuối' => 3,
        'Dừa' => 3,
        'Xoài' => 3,
        'Dưa hấu' => 3,
        'Cam' => 3,
        'Bưởi' => 3,
        'Nho' => 3,
        'Chôm chôm' => 3,
        'Măng cụt' => 3,
        'Ổi' => 3,
        'Lê' => 3,
        'Dâu tây' => 3,
        'Chanh' => 3,
        'Sầu riêng' => 3,
        'Mít' => 3,
        'Bơ ' => 3,
        'Dưa lưới' => 3,
        'Cà phê Robusta' => 4,
        'Cà phê Arabica' => 4,
        'Cà phê moka' => 4,
        'Cà phê cầu Đất' => 4,
        'Cà phê Buôn Ma Thuột' => 4,
        'Chè xanh' => 4,
        'Chè Thái Nguyên' => 4,
        'Hạt điều' => 4,
        'Hạt óc chó' => 4,
        'Bột cacao' => 4,
        'Cacao hạt' => 4,
    ],



    'DEFAULT_PHOTO_PATH' => env('DEFAULT_PHOTO_PATH', 'users/default.jpg'),

    'ITEMS_PER_PAGE' => 10,
];

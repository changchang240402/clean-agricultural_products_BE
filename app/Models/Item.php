<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Maatwebsite\Excel\Facades\Excel;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'item_name',
        'product_id',
        'seller_id',
        'describe',
        'total',
        'price',
        'type',
        'price_type',
        'image',
        'status',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(Review::class, 'review_id', 'id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'item_id', 'id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'target_type', 'id');
    }

    public static function importFromExcel()
    {
    // Đường dẫn tới tệp Excel
        $filePath = '/home/thuytrang/CN-ChangChang/clean-agricultural_products_BE/filedata/shopee.xlsx';

    // Sử dụng thư viện Maatwebsite\Excel để đọc dữ liệu từ tệp Excel và bỏ qua hàng đầu tiên
        $importedData = Excel::toCollection([], $filePath);

        $importedData->first()->splice(0, 1);

    // Khởi tạo một mảng để chứa dữ liệu
        $itemData = [];

    // Lặp qua từng dòng dữ liệu và thêm vào mảng userData
        foreach ($importedData[0] as $row) {
            // Đây là ví dụ, bạn cần điều chỉnh phù hợp với cấu trúc của tệp Excel của bạn
            $itemData[] = [
            'image' => $row[0],
            'item_name' => $row[1],
            'product_id' => $row[3]
            ];
        }

        return $itemData;
    }
}

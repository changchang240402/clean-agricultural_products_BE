<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Maatwebsite\Excel\Facades\Excel;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'review_type',
        'review_id',
        'number_star',
        'content',
        'created_at',
    ];

    public function getRelatedModelAttribute()
    {
        return $this->review_type == 0 ? User::class : Item::class;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function reviewUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'review_id', 'id');
    }

    public function reviewItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'review_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function importFromExcel()
    {
    // Đường dẫn tới tệp Excel
        $filePath = '../clean-agricultural_products_BE/filedata/comments_data_ncds (1).xlsx';

    // Sử dụng thư viện Maatwebsite\Excel để đọc dữ liệu từ tệp Excel và bỏ qua hàng đầu tiên
        $importedData = Excel::toCollection([], $filePath);

        $importedData->first()->splice(0, 1);

    // Khởi tạo một mảng để chứa dữ liệu
        $reviewData = [];

    // Lặp qua từng dòng dữ liệu và thêm vào mảng userData
        foreach ($importedData[0] as $row) {
            // Đây là ví dụ, bạn cần điều chỉnh phù hợp với cấu trúc của tệp Excel của bạn
            $reviewData[] = [
            'content' => $row[0],
            'number_star' => $row[1],
            ];
        }

        return $reviewData;
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'phone',
        'address',
        'birthday',
        'license_plates',
        'driving_license_number',
        'vehicles',
        'payload',
        'role',
        'avatar',
        'status',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function notificationDetails(): HasMany
    {
        return $this->hasMany(NotificationDetail::class, 'user_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'seller_id', 'id');
    }

    public function orderSellers(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id', 'id');
    }

    public function orderTraders(): HasMany
    {
        return $this->hasMany(Order::class, 'trader_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }


    public function attachments(): HasMany
    {
        return $this->hasMany(Review::class, 'review_id', 'id');
    }

    public static function importFromExcel()
    {
    // Đường dẫn tới tệp Excel
        $filePath = '../clean-agricultural_products_BE/filedata/addess.xlsx';

    // Sử dụng thư viện Maatwebsite\Excel để đọc dữ liệu từ tệp Excel và bỏ qua hàng đầu tiên
        $importedData = Excel::toCollection([], $filePath);

        $importedData->first()->splice(0, 1);

    // Khởi tạo một mảng để chứa dữ liệu
        $userData = [];

    // Lặp qua từng dòng dữ liệu và thêm vào mảng userData
        foreach ($importedData[0] as $row) {
            // Đây là ví dụ, bạn cần điều chỉnh phù hợp với cấu trúc của tệp Excel của bạn
            $userData[] = [
            'address' => $row[0],
            ];
        }

        return $userData;
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

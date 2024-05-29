<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'total_price',
        'total_quantity',
        'user_id',
        'seller_id',
        'trader_id',
        'status_review',
        'status',
        'order_date',
        'delivery_date',
        'received_date',
        'order_cancellation_date',
        'cancellation_note',
        'cost',
        'shipping_money',
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'target_id', 'id');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'status', 'id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trader_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

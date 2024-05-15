<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderType extends Model
{
    use HasFactory;

    protected $table = 'order_types';

    protected $fillable = [
        'order_type_name',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'status', 'id');
    }
}

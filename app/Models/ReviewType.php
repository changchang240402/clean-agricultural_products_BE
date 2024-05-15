<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewType extends Model
{
    use HasFactory;

    protected $table = 'review_types';

    protected $fillable = [
        'review_type_name',
    ];

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'status_review', 'id');
    }
}

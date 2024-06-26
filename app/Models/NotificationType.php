<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationType extends Model
{
    use HasFactory;

    protected $table = 'notification_types';

    protected $fillable = [
        'notification_type_name',
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notification_type_id', 'id');
    }
}

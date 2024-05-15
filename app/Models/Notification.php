<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'notification_type_id',
        'target_type',
        'target_id',
        'title',
        'describe',
        'link',
    ];

    public function targetItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'target_id', 'id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'target_id', 'id');
    }

    public function notificationDetails(): HasMany
    {
        return $this->hasMany(NotificationDetail::class, 'notification_id', 'id');
    }

    public function notificationType(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id', 'id');
    }
}

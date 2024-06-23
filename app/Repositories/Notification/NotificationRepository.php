<?php

namespace App\Repositories\Notification;

use App\Models\Notification;
use App\Repositories\BaseRepository;
use App\Repositories\Notification\NotificationRepositoryInterface;
use Exception;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function getModel()
    {
        return Notification::class;
    }

    public function getNotification()
    {
        $userId = auth()->id();
        $user = auth()->user();
        $notifications = [];
        switch ($user->role) {
            case 0:
                $notifications = [];
                break;
            case 1:
                $notifications = $this->model
                    ->where('target_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 2:
                $notifications = $this->model
                    ->where('target_id', $userId)
                    ->orWhereNull('target_id')
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            case 3:
                $notifications = $this->model
                    ->where('target_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return $notifications;
    }
}

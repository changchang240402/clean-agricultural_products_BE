<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\Notification;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(
        NotificationService $notificationService,
    ) {
        $this->notificationService = $notificationService;
    }

    public function getNotification()
    {
        try {
            $notifi = $this->notificationService->getNotification();
            $unreadCount = $notifi->where('read', 0)->count();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'notifi' => $notifi,
            'count' => $unreadCount,
        ], 200);
    }

    public function deleteNotification($id)
    {
        try {
            Notification::where('id', $id)->delete();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function updateNotifications()
    {
        $userId = auth()->id();
        try {
            Notification::where('target_id', $userId)->update(['read' => 1]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
}

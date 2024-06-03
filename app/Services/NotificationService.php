<?php

namespace App\Services;

use App\Repositories\Notification\NotificationRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    protected $notificationRepository;

    public function __construct(
        NotificationRepository $notificationRepository,
    ) {
        $this->notificationRepository = $notificationRepository;
    }

    public function createNotification($data)
    {
        return $this->notificationRepository->create($data);
    }
}

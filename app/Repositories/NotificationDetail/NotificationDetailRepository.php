<?php

namespace App\Repositories\NotificationDetail;

use App\Models\NotificationDetail;
use App\Repositories\BaseRepository;
use App\Repositories\NotificationDetail\NotificationDetailRepositoryInterface;
use Exception;

class NotificationDetailRepository extends BaseRepository implements NotificationDetailRepositoryInterface
{
    public function getModel()
    {
        return NotificationDetail::class;
    }
}

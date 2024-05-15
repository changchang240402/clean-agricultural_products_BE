<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\User\UserRepositoryInterface;
use Exception;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return User::class;
    }

    public function getSellersToUser()
    {
        return $this->model
            ->where('role', '=', config('constants.ROLE')['seller'])
            ->where('status', '=', config('constants.STATUS_USER')['in use'])
            ->select('id', 'name', 'address', 'avatar')
            ->withCount([
                'items as total_items' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['in use']);
                }
            ])
            ->get();
    }

    public function getSellerToAdmin()
    {
        $status = [0, 1];
        return $this->model
            ->where('role', '=', config('constants.ROLE')['seller'])
            ->where('status', '=', config('constants.STATUS_USER')['in use'])
            ->withCount([
                'items as total_items_accept' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['accept']);
                },
                'items as total_items_in_use' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['in use']);
                },
                'items as total_items_archived' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['archived']);
                },
                'items as total_items' => function ($query) use ($status) {
                    $query->whereIn('status', $status);
                }
            ])
            ->get();
    }
}

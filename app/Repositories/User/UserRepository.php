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

    public function sellerDetailById($id)
    {
        $status = [0, 1];
        $seller = $this->model->where('id', $id)
        ->where('role', '=', config('constants.ROLE')['seller'])
        ->with('orderSellers.orderDetails')
        ->with('items.attachments')
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
        ->first();
        if (!$seller) {
            throw new Exception('Seller not found');
        }
        $totalSell = $seller->orderSellers->flatMap(function ($orderSeller) {
            return $orderSeller->status == 4 ? $orderSeller->orderDetails : null;
        })->count();
        $totalReview = 0;
        $totalScore = 0;

        foreach ($seller->items as $item) {
            foreach ($item->attachments as $attachment) {
                if ($attachment->review_type == config('constants.REVIEW')['item']) {
                    $totalReview++;
                    $totalScore += $attachment->number_star;
                }
            }
        }
        $averageScore = $totalReview > 0 ? round($totalScore / $totalReview, 1) : 0;
        return [
            'id' => $seller->id,
            'name' => $seller->name,
            'phone' => $seller->phone,
            'address' => $seller->address,
            'avatar' => $seller->avatar,
            'status' => $seller->status,
            'total_items_accept' => $seller->total_items_accept,
            'total_items_in_use' => $seller->total_items_in_use,
            'total_items_archived' => $seller->total_items_archived,
            'total_items' => $seller->total_items,
            'total_sell' => $totalSell,
            'total_review' => $totalReview,
            'total_score' => $totalScore,
            'average_score' => $averageScore,
        ];
    }
}

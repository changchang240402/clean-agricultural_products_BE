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
            ->withCount([
                'items as total_items_accept' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['accept']);
                },
                'items as total_items_in_use' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['in use']);
                },
                'items as total_items_archived' => function ($query) {
                    $query->where('status', '=', config('constants.STATUS_ITEM')['archived']);
                }
            ])
            ->withCount('items')
            ->get();
    }

    public function getUserToAdmin()
    {
        $status = [5, 6];
        return $this->model
            ->where('role', '=', config('constants.ROLE')['user'])
            ->withCount([
                'orders as total_order' => function ($query) {
                    $query->where('status', '!=', 1);
                },
                'orders as total_order_remove' => function ($query) use ($status) {
                    $query->whereIn('status', $status);
                }
            ])
            ->get();
    }

    public function getTraderToAdmin()
    {
        return $this->model
            ->where('role', '=', config('constants.ROLE')['trader'])
            ->orderBy('created_at', 'desc')
            ->withCount([
                'attachments as total_reviews' => function ($query) {
                    $query->where('review_type', '=', config('constants.REVIEW')['user']);
                },
            ])
            ->with([
                'attachments' => function ($query) {
                    $query->where('review_type', '=', config('constants.REVIEW')['user']);
                }
            ])
            ->get()
            ->map(function ($trader) {
                $totalReviews = $trader->total_reviews;
                $totalScore =  $trader->attachments->sum(function ($attachment) {
                    return $attachment->review_type == config('constants.REVIEW')['user'] ? $attachment->number_star : 0;
                });
                $averageScore = $totalReviews > 0 ? round($totalScore / $totalReviews, 1) : 0;
                $trader->average_review_score = $averageScore;
                return [
                    'id' => $trader->id,
                    'name' => $trader->name,
                    'email' => $trader->email,
                    'birthday' => $trader->birthday,
                    'license_plates' => $trader->license_plates,
                    'driving_license_number' => $trader->driving_license_number,
                    'vehicles' => $trader->vehicles,
                    'avatar' => $trader->avatar,
                    'total_reviews' => $trader->total_reviews,
                    'status' => $trader->status,
                    'average_review_score' => $trader->average_review_score,
                ];
            });
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

    public function totalUserByUserId($month, $year)
    {
        $user = $this->model->where('role', '!=', config('constants.ROLE')['admin'])->count();
        $user_now = $this->model
        ->where('role', '!=', config('constants.ROLE')['admin'])
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->count();
        return [
            'user' => $user,
            'user_now' => $user_now
        ];
    }
}

<?php

namespace App\Repositories\Review;

use App\Models\Item;
use App\Models\Review;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function getModel()
    {
        return Review::class;
    }

    public function getReviewsToId($id, $type)
    {
        return $this->model
        ->where('review_type', $type)
        ->where('review_id', $id)
        ->with([
            'user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }
        ])
        ->get();
    }

    public function starStatistics($id, $type)
    {
        $totalStart = $this->model
        ->where('review_type', $type)
        ->where('review_id', $id)
        ->sum('number_star');

    // Get the count of reviews for each star rating
        $starCounts = $this->model
        ->where('review_type', $type)
        ->where('review_id', $id)
        ->select('number_star')
        ->get()
        ->groupBy('number_star')
        ->map(function ($ratingGroup) {
            return $ratingGroup->count();
        })
        ->toArray();
        $starRatings = [0,0,0,0,0];

        // Populate the star ratings array with actual counts
        foreach ($starCounts as $number_star => $count) {
            if ($number_star >= 1 && $number_star <= 5) {
                $starRatings[$number_star - 1] = $count;
            }
        }
        return [
            'star' => $starRatings,
            'totalStart' => $totalStart,
        ];
    }
    public function totalReviewByUserId($userId, $role, $month, $year)
    {
        $review = 0;
        $review_now = 0;
        switch ($role) {
            case 2:
                $review_seller = $this->model
                ->where('review_type', '=', config('constants.REVIEW')['user'])
                ->where('review_id', $userId)
                ->count();
                $item = Item::where('seller_id', $userId)->pluck('id')->toArray();
                $review_item = $this->model
                ->where('review_type', '=', config('constants.REVIEW')['item'])
                ->whereIn('review_id', $item)
                ->count();
                $review = $review_item + $review_seller;
                $review_seller_now = $this->model
                ->where('review_type', '=', config('constants.REVIEW')['user'])
                ->where('review_id', $userId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
                $review_item_now = $this->model
                ->where('review_type', '=', config('constants.REVIEW')['item'])
                ->whereIn('review_id', $item)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
                $review_now = $review_item_now + $review_seller_now;
                break;
            case 3:
                $review = $this->model
                ->where('review_type', '=', config('constants.REVIEW')['user'])
                ->where('review_id', $userId)
                ->count();
                $review_now = $this->model
                ->where('review_type', '=', config('constants.REVIEW')['user'])
                ->where('review_id', $userId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
                break;
            default:
                return response()->json(['message' => 'Unknown Role'], 400);
        }
        return [
            'review' => $review,
            'review_now' => $review_now
        ];
    }
}

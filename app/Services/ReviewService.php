<?php

namespace App\Services;

use App\Repositories\Review\ReviewRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    protected $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository,
    ) {
        $this->reviewRepository = $reviewRepository;
    }

    public function getReviewsToId(
        int $id,
        int $type,
    ) {
        $star =  $this->reviewRepository->starStatistics($id, $type);
        $review =  $this->reviewRepository->getReviewsToId($id, $type);
        return [
            'star' => $star,
            'review' => $review
        ];
    }
}

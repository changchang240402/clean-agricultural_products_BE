<?php

namespace App\Repositories\Review;

use App\Models\Review;
use App\Repositories\BaseRepository;
use Exception;

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
}

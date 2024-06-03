<?php

namespace App\Repositories\Review;

use App\Repositories\RepositoryInterface;

interface ReviewRepositoryInterface extends RepositoryInterface
{
    public function getReviewsToId($id, $type);

    public function totalReviewByUserId($userId, $role, $month, $year);
}

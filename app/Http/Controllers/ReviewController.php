<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\FilterReviewRequest;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(
        ReviewService $reviewService,
    ) {
        $this->reviewService = $reviewService;
    }

    public function getReviewsToId(FilterReviewRequest $request)
    {
        $validated = $request->validated();
        $id = $validated['id'];
        $type = $validated['type'];
        try {
            $review = $this->reviewService->getReviewsToId($id, $type);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'review' => $review,
        ], 200);
    }
}

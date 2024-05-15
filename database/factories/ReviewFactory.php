<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = [1,2];
        $createdAt = fake()->dateTimeBetween('-3 year', 'now');
        $randomNumber = random_int(0, 10);
        if ($randomNumber < 5) {
            $userId = User::whereIn('role', $role)
                            ->inRandomOrder()->get()->random()->id;
            $reviewType = config('constants.REVIEW')['user'];
            $reviewId = User::where('role', '=', config('constants.ROLE')['trader'])
                            ->inRandomOrder()->get()->random()->id;
        } else {
            $userId = User::where('role', '=', config('constants.ROLE')['user'])
                            ->inRandomOrder()->get()->random()->id;
            $reviewType = config('constants.REVIEW')['item'];
            $reviewId = Item::all()->random()->id;
            // $reviewId = Item::where('status', '=', config('constants.STATUS_ITEM')['in use'])
            // ->inRandomOrder()->get()->random()->id;
        }
        return [
            'user_id' => $userId,
            'review_type' => $reviewType,
            'review_id' => $reviewId,
            'created_at' => $createdAt,
        ];
    }
}

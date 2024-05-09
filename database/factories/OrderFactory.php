<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role1 = [8,9,10,11];
        $role2 = [6,7];
        $review1 = ['1,3', '1,4', '1,3,4'];
        $statusrole = [1,2];
        $traderId1 = User::where('role', '=', config('constants.ROLE')['trader'])
                        ->whereIn('status', $statusrole)
                        ->inRandomOrder()->get()->random()->id;
        $traderId2 = User::where('role', '=', config('constants.ROLE')['trader'])
                        ->where('status', '=', 3)
                        ->inRandomOrder()->get()->random()->id;
        $check = fake()->boolean();
        $randomNumber = random_int(0, 10);
        $createdAt = fake()->dateTimeBetween('-3 year', 'now');
        $updatedAt = fake()->dateTimeBetween($createdAt, 'now');
        $orderDate1 = fake()->dateTimeBetween($createdAt, 'now');
        $deliveryDate1 = fake()->dateTimeBetween($orderDate1, 'now');
        $receivedDate1 = fake()->dateTimeBetween($deliveryDate1, 'now');
        $sellerId = User::where('role', '=', config('constants.ROLE')['seller'])->inRandomOrder()->get()->random()->id;
        $userId = User::where('role', '=', config('constants.ROLE')['user'])->inRandomOrder()->get()->random()->id;
        if ($randomNumber < 7) {
            $totalPrice = fake()->numberBetween(5000, 20000) * 1000;
            $totalQuantity = fake()->numberBetween(50, 100) * 10;
            $traderId = $check ? $traderId1 : $traderId2;
            $statusReview = $check ? $review1[array_rand($review1)] : '1';
            $status = $check ? $role1[array_rand($role1)] : $role2[array_rand($role2)];
            $orderDate = $orderDate1;
            $deliveryDate = $deliveryDate1;
            $receivedDate = $receivedDate1;
        } else {
            $totalPrice = 0;
            $totalQuantity = 0;
            $traderId = null;
            $statusReview = '1';
            $status = 1;
            $orderDate = null;
            $deliveryDate = null;
            $receivedDate = null;
        }
        if ($randomNumber < 3) {
            $orderCancellationDate = fake()->dateTimeBetween($orderDate1, 'now');
            $cancellationNote = 'Không còn nhu cầu mua';
        } else {
            $orderCancellationDate = null;
            $cancellationNote = null;
        }
        return [
            'total_price' => $totalPrice,
            'total_quantity' => $totalQuantity,
            'user_id' => $userId,
            'seller_id' => $sellerId,
            'trader_id' => $traderId,
            'status_review' => $statusReview,
            'status' => $status,
            'order_date' => $orderDate,
            'delivery_date' => $deliveryDate,
            'received_date' => $receivedDate,
            'order_cancellation_date' => $orderCancellationDate,
            'cancellation_note' => $cancellationNote,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}

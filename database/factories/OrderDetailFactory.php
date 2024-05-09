<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = Order::all()->random();
        $createdAt = fake()->dateTimeBetween('-3 year', 'now');
        $updatedAt = fake()->dateTimeBetween($createdAt, 'now');
        $itemId = Item::where('seller_id', '=', $order->seller_id)->inRandomOrder()->get()->random()->id;
        $status = random_int(1, 2);
        $count = random_int(1, 10);
        return [
            'order_id' => $order->id,
            'item_id' => $itemId,
            'status_review' => $status,
            'count' => $count,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}

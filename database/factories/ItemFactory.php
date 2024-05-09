<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sellerId = User::where('role', '=', config('constants.ROLE')['seller'])->inRandomOrder()->get()->random()->id;
        $createdAt = fake()->dateTimeBetween('-3 year', 'now');
        $updatedAt = fake()->dateTimeBetween($createdAt, 'now');
        $describe = 'Sản phẩm sạch an toàn 100% trong theo phương pháp hữu cơ không có thuốc trừ sâu.';
        $total = fake()->numberBetween(10, 500) * 10;
        $price = fake()->numberBetween(2000, 10000) * 10;
        $type = fake()->numberBetween(10, 20) * 10;
        $price_type = $type * $price;
        $status = random_int(0, 3);
        return [
            'seller_id' => $sellerId,
            'describe' => $describe,
            'total' => $total,
            'price' => $price,
            'type' => $type,
            'price_type' => $price_type,
            'status' => $status,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}

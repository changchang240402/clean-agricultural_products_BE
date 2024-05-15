<?php

namespace Database\Seeders;

use App\Models\OrderType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orderType = config('constants.ORDER_TYPES');

        for ($i = 0; $i < count($orderType); $i++) {
            OrderType::factory()->create([
                'order_type_name' => $orderType[$i]
            ]);
        }
    }
}

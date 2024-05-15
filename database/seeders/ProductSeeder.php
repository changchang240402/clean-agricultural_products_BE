<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = config('constants.PRODUCT');

        foreach ($products as $productName => $productTypeId) {
            $price_min = fake()->numberBetween(10, 60);
            $price_max = fake()->numberBetween($price_min, 99);
            Product::create([
                'product_name' => $productName,
                'product_type_id' => $productTypeId,
                'price_max' => $price_max * 1000,
                'price_min' => $price_min * 1000,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productType = config('constants.PRODUCT_TYPES');

        for ($i = 0; $i < count($productType); $i++) {
            ProductType::factory()->create([
                'product_type_name' => $productType[$i]
            ]);
        }
    }
}

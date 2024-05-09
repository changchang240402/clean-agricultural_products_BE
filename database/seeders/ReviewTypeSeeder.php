<?php

namespace Database\Seeders;

use App\Models\ReviewType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviewType = config('constants.REVIEW_TYPES');

        for ($i = 0; $i < count($reviewType); $i++) {
            ReviewType::factory()->create([
                'review_type_name' => $reviewType[$i]
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $excelData = Review::importFromExcel();

        // Tạo bản ghi với dữ liệu từ Excel và factory
        foreach ($excelData as $data) {
            Review::factory()->create(array_merge($data, [
                'content' => $data['content'],
                'number_star' => $data['number_star'],
            ]));
        }
    }
}

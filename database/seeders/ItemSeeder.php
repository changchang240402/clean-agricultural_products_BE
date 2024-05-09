<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $excelData = Item::importFromExcel();

        // Tạo bản ghi với dữ liệu từ Excel và factory
        foreach ($excelData as $data) {
            Item::factory()->create(array_merge($data, [
                'item_name' => $data['item_name'],
                'product_id' => $data['product_id'],
                'image' => $data['image'],
            ]));
        }
    }
}

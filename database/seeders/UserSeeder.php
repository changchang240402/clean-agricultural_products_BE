<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Đào Thủy Trang',
            'email' => 'thuytrangdao240402@gmail.com',
            'password' => bcrypt('24042002'),
            'phone' => '0343969468',
            'address' => '9, Bầu Tràm Trung, Khuê Trung, Cẩm Lệ, Đà Nẵng',
            'role' => 0,
            'status' => 1,
            'avatar' => 'https://cdn.tgdd.vn/Files/2021/07/22/1370175/su-tich-y-nghia-cach-trong-va-cham-soc-hoa-linh-lan-202107222154164395.jpg'
        ]);

        $excelData = User::importFromExcel();

        // Tạo bản ghi với dữ liệu từ Excel và factory
        foreach ($excelData as $data) {
            User::factory()->create(array_merge($data, [
                'address' => $data['address'], // Sử dụng dữ liệu 'address' từ Excel
            ]));
        }
    }
}

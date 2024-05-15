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
            'email' => 'thuytrang2404@gmail.com',
            'password' => bcrypt('24042002'),
            'phone' => '0343969468',
            'address' => '15, đường Hồ Xuân Hương, Thành phố Tam Điệp, Ninh Bình',
            'role' => 0,
            'status' => 1,
            'avatar' => 'https://cdn.tgdd.vn/Files/2021/07/22/1370175/su-tich-y-nghia-cach-trong-va-cham-soc-hoa-linh-lan-202107222154164395.jpg'
        ]);
        User::create([
            'name' => 'Lê Anh Thư',
            'email' => 'thuytrangdao240402@gmail.com',
            'password' => bcrypt('24042002'),
            'phone' => '0979621395',
            'address' => '9, Bầu Tràm Trung, Khuê Trung, Cẩm Lệ, Đà Nẵng',
            'role' => 2,
            'status' => 1,
            'avatar' => 'https://originmarket.vn/wp-content/uploads/2021/11/cach-chon-hoa-qua-phu-hop-trong-che-do-an-uong-han-che-duong-1-768x483-1.jpg'
        ]);

        User::create([
            'name' => 'Trịnh Hồng Sâm',
            'email' => 'trang.dao@dac-datatech.vn',
            'password' => bcrypt('24042002'),
            'phone' => '0962599268',
            'address' => '15/15, Phan Châu Trinh, Hải Châu 1, Đà Nẵng',
            'birthday' => '1998-05-21',
            'license_plates' => '34A-123.09',
            'driving_license_number' => '123456789012',
            'vehicles' => 'Isuzu QKR230',
            'payload' => 1200,
            'role' => 3,
            'status' => 1,
            'avatar' => 'https://images2.thanhnien.vn/528068263637045248/2023/11/8/2-trai-cay-shutterstock-1699428905461213188394.jpg'
        ]);
        User::create([
            'name' => 'Trịnh La Hoan',
            'email' => 'baocatsamac1824@gmail.com',
            'password' => bcrypt('24042002'),
            'phone' => '0316027829',
            'address' => '15/9, Phường Trung Sơn, Thành phố Tam Điệp, Ninh Bình',
            'role' => 1,
            'status' => 1,
            'avatar' => 'https://originmarket.vn/wp-content/uploads/2021/11/cach-chon-hoa-qua-phu-hop-trong-che-do-an-uong-han-che-duong-1-768x483-1.jpg'
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

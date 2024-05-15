<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ReviewType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
           UserSeeder::class,
           ProductTypeSeeder::class,
           ProductSeeder::class,
           OrderTypeSeeder::class,
           ReviewTypeSeeder::class,
           NotificationTypeSeeder::class,
           ItemSeeder::class,
           OrderSeeder::class,
           OrderDetailSeeder::class,
           ReviewSeeder::class,
        ]);
    }
}

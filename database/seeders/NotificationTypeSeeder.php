<?php

namespace Database\Seeders;

use App\Models\NotificationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notificationType = config('constants.NOTIFICATION_TYPES');

        for ($i = 0; $i < count($notificationType); $i++) {
            NotificationType::factory()->create([
                'notification_type_name' => $notificationType[$i]
            ]);
        }
    }
}

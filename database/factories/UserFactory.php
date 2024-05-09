<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vehicleList = config('constants.VEHICLES');
        $randomNumber = random_int(0, 10);
        $createdAt = fake()->dateTimeBetween('-3 year', 'now');
        $updatedAt = fake()->dateTimeBetween($createdAt, 'now');
        if ($randomNumber < 7) {
            $role = random_int(1, 2); // 'user','seller'
            $status = random_int(1, 2);
            $birthday = null;
            $licensePlates = null;
            $drivingLicenseNumber = null;
            $vehicles = null;
            $payload = null;
        } else {
            $role = 3; // 'trader'
            $status = random_int(0, 3);
            $birthday = fake()->dateTimeBetween('-50 year', '-19 year')->format('Y-m-d');
            $licensePlates = fake('vi_VN')->unique()->regexify('[0-9]{2}[A-Z]{1}-[0-9]{3}\.[0-9]{2}');
            $drivingLicenseNumber = fake('vi_VN')->unique()->regexify('[0-9]{12}');
            $vehicles = $vehicleList[random_int(0, 12)];
            $payload = fake()->numberBetween(1, 50) * 100;
        }

        return [
            'name' => fake('vi_VN')->lastName()
                . ' ' . fake('vi_VN')->middleName()
                . ' ' . fake('vi_VN')->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake('vi_VN')->unique()->regexify('(0){1}([0-9]{9})'),
            'birthday' => $birthday,
            'license_plates' => $licensePlates,
            'driving_license_number' => $drivingLicenseNumber,
            'vehicles' => $vehicles,
            'payload' => $payload,
            'avatar' => fake()->imageUrl(360, 360, 'animals', true),
            'password' => static::$password ??= Hash::make('12345678'),
            'role' => $role,
            'status' => $status,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

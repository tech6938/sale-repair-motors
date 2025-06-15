<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'staff1@domain.com')->firstOrFail();

        $fuelTypes = [
            Vehicle::FUEL_TYPE_GASOLINE,
            Vehicle::FUEL_TYPE_DIESEL,
            Vehicle::FUEL_TYPE_ELECTRIC,
            Vehicle::FUEL_TYPE_HYBRID,
        ];

        $vehicles = [
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Nissan',
                'model' => 'Murano',
                'year' => 2022,
                'fuel_type' => fake()->randomElement($fuelTypes),
                'color' => 'Black',
                'license_plate' => 'NIS2022',
                'milage' => fake()->randomFloat(2, 1000, 50000),
                'registration' => 'REG-NIS2022',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2021,
                'fuel_type' => fake()->randomElement($fuelTypes),
                'color' => 'Silver',
                'license_plate' => 'TOY2021',
                'milage' => fake()->randomFloat(2, 1000, 50000),
                'registration' => 'REG-TOY2021',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2023,
                'fuel_type' => fake()->randomElement($fuelTypes),
                'color' => 'Blue',
                'license_plate' => 'FRD2023',
                'milage' => fake()->randomFloat(2, 1000, 50000),
                'registration' => 'REG-FRD2023',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Chevrolet',
                'model' => 'Tahoe',
                'year' => 2020,
                'fuel_type' => fake()->randomElement($fuelTypes),
                'color' => 'White',
                'license_plate' => 'CHV2020',
                'milage' => fake()->randomFloat(2, 1000, 50000),
                'registration' => 'REG-CHV2020',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Honda',
                'model' => 'Accord',
                'year' => 2022,
                'fuel_type' => fake()->randomElement($fuelTypes),
                'color' => 'Red',
                'license_plate' => 'HND2022',
                'milage' => fake()->randomFloat(2, 1000, 50000),
                'registration' => 'REG-HND2022',
                'created_at' => now(),
            ],
        ];

        Vehicle::insert($vehicles);
    }
}

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

        $vehicles = [
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Nissan',
                'model' => 'Patrol',
                'year' => 2022,
                'fuel_type' => 'Gasoline',
                'address' => 'Houston, TX, USA',
                'color' => 'Black',
                'price' => 25000.00,
                'license_plate' => 'NIS2022',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2021,
                'fuel_type' => 'Hybrid',
                'address' => 'Dallas, TX, USA',
                'color' => 'Silver',
                'price' => 22000.00,
                'license_plate' => 'TOY2021',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2023,
                'fuel_type' => 'Diesel',
                'address' => 'Austin, TX, USA',
                'color' => 'Blue',
                'price' => 35000.00,
                'license_plate' => 'FRD2023',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Chevrolet',
                'model' => 'Tahoe',
                'year' => 2020,
                'fuel_type' => 'Gasoline',
                'address' => 'San Antonio, TX, USA',
                'color' => 'White',
                'price' => 32000.00,
                'license_plate' => 'CHV2020',
                'created_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'uuid' => getUuid(),
                'make' => 'Honda',
                'model' => 'Accord',
                'year' => 2022,
                'fuel_type' => 'Gasoline',
                'address' => 'El Paso, TX, USA',
                'color' => 'Red',
                'price' => 24000.00,
                'license_plate' => 'HND2022',
                'created_at' => now(),
            ],
        ];

        Vehicle::insert($vehicles);
    }
}

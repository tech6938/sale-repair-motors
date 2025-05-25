<?php

namespace Database\Seeders;

use App\Models\InspectionType;
use Illuminate\Database\Seeder;

class InspectionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InspectionType::create([
            'uuid' => getUuid(),
            'title' => 'Vehicle Inspection',
            'description' => fake()->sentence(),
            'is_active' => true,
            'created_at' => now(),
        ]);
    }
}

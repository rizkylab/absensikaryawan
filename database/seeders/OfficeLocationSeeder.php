<?php

namespace Database\Seeders;

use App\Models\OfficeLocation;
use Illuminate\Database\Seeder;

class OfficeLocationSeeder extends Seeder
{
    public function run(): void
    {
        OfficeLocation::create([
            'name' => 'Head Office',
            'latitude' => -6.200000, // Jakarta coordinates (example)
            'longitude' => 106.816666,
            'radius' => 100, // 100 meters
            'is_active' => true,
        ]);
    }
}

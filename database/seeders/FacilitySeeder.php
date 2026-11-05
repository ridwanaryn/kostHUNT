<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Seed the fixed facility list that owners choose from.
     */
    public function run(): void
    {
        $facilities = [
            ['name' => 'AC', 'icon' => 'ac'],
            ['name' => 'Private Bathroom', 'icon' => 'bathroom'],
            ['name' => 'Wi-Fi', 'icon' => 'wifi'],
            ['name' => 'Bed', 'icon' => 'bed'],
            ['name' => 'Wardrobe', 'icon' => 'wardrobe'],
            ['name' => 'Desk', 'icon' => 'desk'],
            ['name' => 'Water Heater', 'icon' => 'heater'],
            ['name' => 'Kitchen', 'icon' => 'kitchen'],
            ['name' => 'Washing Machine', 'icon' => 'washer'],
            ['name' => 'Parking', 'icon' => 'parking'],
            ['name' => 'Security', 'icon' => 'security'],
            ['name' => 'CCTV', 'icon' => 'cctv'],
            ['name' => 'Cleaning Service', 'icon' => 'cleaning'],
        ];

        foreach ($facilities as $facility) {
            Facility::updateOrCreate(
                ['name' => $facility['name']],
                ['icon' => $facility['icon']],
            );
        }
    }
}

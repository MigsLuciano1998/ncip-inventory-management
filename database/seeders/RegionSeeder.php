<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['region_short_name' => 'NCR', 'region_complete_name' => 'National Capital Region'],
            ['region_short_name' => 'CAR', 'region_complete_name' => 'Cordillera Administrative Region'],
            ['region_short_name' => 'Region I', 'region_complete_name' => 'Ilocos Region'],
            ['region_short_name' => 'Region II', 'region_complete_name' => 'Cagayan Valley'],
            ['region_short_name' => 'Region III', 'region_complete_name' => 'Central Luzon'],
            ['region_short_name' => 'Region IV-A', 'region_complete_name' => 'CALABARZON'],
            ['region_short_name' => 'Region IV-B', 'region_complete_name' => 'MIMAROPA'],
            ['region_short_name' => 'Region V', 'region_complete_name' => 'Bicol Region'],
            ['region_short_name' => 'Region VI', 'region_complete_name' => 'Western Visayas'],
            ['region_short_name' => 'Region VII', 'region_complete_name' => 'Central Visayas'],
            ['region_short_name' => 'Region VIII', 'region_complete_name' => 'Eastern Visayas'],
            ['region_short_name' => 'Region IX', 'region_complete_name' => 'Zamboanga Peninsula'],
            ['region_short_name' => 'Region X', 'region_complete_name' => 'Northern Mindanao'],
            ['region_short_name' => 'Region XI', 'region_complete_name' => 'Davao Region'],
            ['region_short_name' => 'Region XII', 'region_complete_name' => 'SOCCSKSARGEN'],
            ['region_short_name' => 'Region XIII', 'region_complete_name' => 'Caraga'],
            ['region_short_name' => 'BARMM', 'region_complete_name' => 'Bangsamoro Autonomous Region in Muslim Mindanao'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['region_short_name' => $region['region_short_name']],
                [
                    'region_complete_name' => $region['region_complete_name'],
                    'status' => true,
                ]
            );
        }
    }
}

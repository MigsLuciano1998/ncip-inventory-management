<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Region;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $car = Region::where('region_short_name', 'CAR')->first();

        if (!$car) {
            return;
        }


        $provinces = [
            'Abra',
            'Apayao',
            'Baguio City',
            'Benguet',
            'Ifugao',
            'Kalinga',
            'Mountain Province',
            'Region',
        ];


        foreach ($provinces as $province) {
            Province::firstOrCreate(
                [
                    'region_id' => $car->id,
                    'province_name' => $province,
                ],
                [
                    'status' => true,
                ]
            );
        }
    }
}
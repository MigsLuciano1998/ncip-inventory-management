<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use Illuminate\Database\Seeder;

class EquipmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'Building', 'short_name' => 'BLDG'],
            ['title' => 'Computer Software', 'short_name' => 'CS'],
            ['title' => 'Furniture and Fixtures', 'short_name' => 'OE'],
            ['title' => 'Information and Communications Technology Equipment', 'short_name' => 'ICT'],
            ['title' => 'Land', 'short_name' => 'LAND'],
            ['title' => 'Motor Vehicle', 'short_name' => 'MV'],
            ['title' => 'Office Equipment', 'short_name' => 'OE'],
            ['title' => 'Technical and Scientific Equipment', 'short_name' => 'TSE'],
        ];

        foreach ($categories as $category) {
            EquipmentCategory::updateOrCreate(
                ['title' => $category['title']],
                [
                    'short_name' => $category['short_name'],
                    'status' => true,
                    'date' => now()->toDateString(),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipmentTypes = [

            'Desktop Computer',
            'Laptop',
            'Printer',
            'Scanner',
            'Photocopier',
            'Projector',
            'UPS',
            'Monitor',
            'Server',
            'NAS',
            'Router',
            'Network Switch',
            'Access Point',
            'Firewall',
            'IP Phone',
            'Tablet',
            'Biometric Device',
            'Web Camera',
            'External Hard Drive',
            'Barcode Scanner',

        ];

        $ictCategoryId = EquipmentCategory::query()
            ->where('short_name', 'ICT')
            ->value('id');

        foreach ($equipmentTypes as $type) {

            EquipmentType::updateOrCreate(
                ['name' => $type],
                [
                    'equipment_category_id' => $ictCategoryId,
                    'status' => true,
                ]
            );

        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\PpeCategory;
use Illuminate\Database\Seeder;

class PpeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['number' => '1', 'title' => 'Land', 'uacs_object_code' => '1-06-01-01-000', 'ppe_sub_major_account_group' => '01', 'general_ledger_account' => '01'],
            ['number' => '2', 'title' => 'Buildings', 'uacs_object_code' => '1-06-04-01-000', 'ppe_sub_major_account_group' => '04', 'general_ledger_account' => '01'],
            ['number' => '3', 'title' => 'Office Equipment', 'uacs_object_code' => '1-06-05-02-000', 'ppe_sub_major_account_group' => '05', 'general_ledger_account' => '02'],
            ['number' => '4', 'title' => 'Information and Communications Technology Equipment', 'uacs_object_code' => '1-06-05-03-000', 'ppe_sub_major_account_group' => '05', 'general_ledger_account' => '03'],
            ['number' => '5', 'title' => 'Technical and Scientific Equipment', 'uacs_object_code' => '1-06-05-14-000', 'ppe_sub_major_account_group' => '05', 'general_ledger_account' => '14'],
            ['number' => '6', 'title' => 'Motor Vehicles', 'uacs_object_code' => '1-06-06-01-000', 'ppe_sub_major_account_group' => '06', 'general_ledger_account' => '01'],
            ['number' => '7', 'title' => 'Computer Software', 'uacs_object_code' => '1-08-01-02-000', 'ppe_sub_major_account_group' => '01', 'general_ledger_account' => '02'],
            ['number' => '8', 'title' => 'Furniture and Fixtures', 'uacs_object_code' => '1-06-07-01-000', 'ppe_sub_major_account_group' => '07', 'general_ledger_account' => '01'],
            ['number' => '3A', 'title' => 'Semi-Expendable Office Equipment', 'uacs_object_code' => '1-04-05-02-000', 'ppe_sub_major_account_group' => '05', 'general_ledger_account' => '02'],
            ['number' => '4A', 'title' => 'Semi-Expendable Information and Communications Technology Equipment', 'uacs_object_code' => '1-04-05-03-000', 'ppe_sub_major_account_group' => '05', 'general_ledger_account' => '03'],
            ['number' => '5A', 'title' => 'Semi-Expendable Technical and Scientific Equipment', 'uacs_object_code' => '1-04-05-13-000', 'ppe_sub_major_account_group' => '05', 'general_ledger_account' => '13'],
            ['number' => '8A', 'title' => 'Semi-Expendable Furniture and Fixtures', 'uacs_object_code' => '1-04-06-01-000', 'ppe_sub_major_account_group' => '06', 'general_ledger_account' => '01'],
            ['number' => '9A', 'title' => 'Semi-Expendable Medical Equipment', 'uacs_object_code' => '1-04-05-01-000', 'ppe_sub_major_account_group' => '05', 'general_ledger_account' => '01'],
        ];

        foreach ($categories as $category) {
            PpeCategory::updateOrCreate(
                ['number' => $category['number']],
                [
                    'title' => $category['title'],
                    'uacs_object_code' => $category['uacs_object_code'],
                    'ppe_sub_major_account_group' => $category['ppe_sub_major_account_group'],
                    'general_ledger_account' => $category['general_ledger_account'],
                    'status' => true,
                    'date' => now()->toDateString(),
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\Province;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [

            // APAYAO
            'Apayao' => [
                [
                    'name' => 'Apayao Provincial Office',
                    'location' => '3F National Line Agencies Building, Government Center, San Isidro Sur, Luna, Apayao',
                ],
                [
                    'name' => 'Conner CSC',
                    'location' => 'LGU Compound, Caglayan, Conner, Apayao',
                ],
                [
                    'name' => 'Kabugao CSC',
                    'location' => 'LGU Building Poblacion, Kabugao, Apayao',
                ],
                [
                    'name' => 'Luna CSC',
                    'location' => 'LNB Building, Poblacion, Pudtol, Apayao',
                ],
            ],


            // ABRA
            'Abra' => [
                [
                    'name' => 'Abra Provincial Office',
                    'location' => 'Abra Tingguian Center, Calaba, Bangued, Abra',
                ],
                [
                    'name' => 'San Gregorio CSC',
                    'location' => 'Abra Tingguian Center, Calaba, Bangued, Abra',
                ],
                [
                    'name' => 'Licuan-Baay CSC',
                    'location' => 'Abra Tingguian Center, Calaba, Bangued, Abra',
                ],
                [
                    'name' => 'Manabo CSC',
                    'location' => 'Abra Tingguian Center, Calaba, Bangued, Abra',
                ],
            ],


            // BENGUET
            'Benguet' => [
                [
                    'name' => 'Benguet Provincial Office',
                    'location' => 'Provincial Capitol Compound, La Trinidad, Benguet',
                ],
                [
                    'name' => 'Atok CSC',
                    'location' => 'Sayangan, Paoay, Atok, Benguet',
                ],
                [
                    'name' => 'Bokod CSC',
                    'location' => 'Ambangeg, Daclan, Bokod, Benguet',
                ],
                [
                    'name' => 'Sablan CSC',
                    'location' => 'Acop, Tublay, Benguet',
                ],
                [
                    'name' => 'Itogon CSC',
                    'location' => 'Upper Tram, Ucab, Itogon, Benguet',
                ],
            ],


            // MOUNTAIN PROVINCE
            'Mountain Province' => [
                [
                    'name' => 'Mountain Province Provincial Office',
                    'location' => 'NCIP Building, Poblacion, Bontoc, Mountain Province',
                ],
                [
                    'name' => 'Sabata CSC',
                    'location' => '2nd Floor Liga Building Municipal Compound, Abatan Bauko, Mt. Province',
                ],
                [
                    'name' => 'Sabebosa CSC',
                    'location' => '2nd Floor, Market Building, Patay, Sagada, Mt. Province',
                ],
                [
                    'name' => 'Panaba CSC',
                    'location' => 'Baracks, Poblacion, Natonin, Mountain Province',
                ],
            ],


            // IFUGAO
            'Ifugao' => [
                [
                    'name' => 'Ifugao Provincial Office',
                    'location' => 'NFA Compound, Dotal St., Poblacion South, Lagawe, Ifugao',
                ],
                [
                    'name' => 'Banaue CSC',
                    'location' => 'Tam-an, Banaue, Ifugao',
                ],
                [
                    'name' => 'Aguinaldo CSC',
                    'location' => 'Kabataan Center for Youth Building, Galonogon, Aguinaldo, Ifugao',
                ],
                [
                    'name' => 'Tinoc CSC',
                    'location' => 'Poblacion, Tinoc, Ifugao',
                ],
            ],


            // KALINGA
            'Kalinga' => [
                [
                    'name' => 'Kalinga Provincial Office',
                    'location' => 'NCIP Building, Bulanao, Tabuk City, Kalinga',
                ],
                [
                    'name' => 'Balbalan CSC',
                    'location' => 'Cawagayan, Pinukpuk, Kalinga',
                ],
                [
                    'name' => 'Tanudan CSC',
                    'location' => 'NCIP Building, Bulanao, Tabuk City, Kalinga',
                ],
                [
                    'name' => 'Tinglayan CSC',
                    'location' => 'Poblacion, Tinglayan, Kalinga',
                ],
                [
                    'name' => 'Tinglayan CSC Extension',
                    'location' => 'Lubuagan, Kalinga',
                ],
            ],

            // RO
            'Region' => [
                [
                    'name' => 'CAR Regional Office',
                    'location' => 'Lyman Ogilby Building, Baguio City, Benguet',
                ],
                [
                    'name' => 'Commision on Audit',
                    'location' => 'Lyman Ogilby Building, Baguio City, Benguet',
                ],
                [
                    'name' => 'FASD',
                    'location' => 'Lyman Ogilby Building, Baguio City, Benguet',
                ],
                [
                    'name' => 'ORD',
                    'location' => 'Lyman Ogilby Building, Baguio City, Benguet',
                ],
                [
                    'name' => 'RHO',
                    'location' => 'Lyman Ogilby Building, Baguio City, Benguet',
                ],
                [
                    'name' => 'TMSD',
                    'location' => 'Lyman Ogilby Building, Baguio City, Benguet',
                ],
            ],

            // RO
            'Baguio City' => [
                [
                    'name' => 'Baguio City Office',
                    'location' => 'No. 40 BIBAK/BIMAAK Multipurpose Hall, Claudio Carantes-Harrison, 2600 Baguio City',
                ],
                [
                    'name' => 'Baguio CSC',
                    'location' => 'No. 40 BIBAK/BIMAAK Multipurpose Hall, Claudio Carantes-Harrison, 2600 Baguio City',
                ],
                
            ],

        ];


        foreach ($offices as $provinceName => $provinceOffices) {

            $province = Province::where('province_name', $provinceName)
                ->first();


            if (!$province) {
                continue;
            }


            foreach ($provinceOffices as $office) {

                Office::firstOrCreate(
                    [
                        'province_id' => $province->id,
                        'office_name' => $office['name'],
                    ],
                    [
                        'office_location' => $office['location'],
                        'status' => true,
                    ]
                );

            }
        }
    }
}
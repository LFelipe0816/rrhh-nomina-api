<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Tech Nova',
                'legal_name' => 'Tech Nova SRL',
                'rnc' => '1-01-12345-6',
                'email' => 'contacto@technova.com',
                'phone' => '+1-809-555-1000',
                'website' => 'https://technova.com',
                'address' => 'Av. Lope de Vega 101',
                'city' => 'Santo Domingo',
                'state' => 'DN',
                'country_code' => 'DO',
                'status' => 1,
            ],
            [
                'name' => 'Caribbean Foods',
                'legal_name' => 'Caribbean Foods SRL',
                'rnc' => '1-02-67890-2',
                'email' => 'info@caribbeanfoods.com',
                'phone' => '+1-809-555-2000',
                'website' => 'https://caribbeanfoods.com',
                'address' => 'Calle El Conde 45',
                'city' => 'Santo Domingo',
                'state' => 'DN',
                'country_code' => 'DO',
                'status' => 1,
            ],
            [
                'name' => 'Atlantic Assurance',
                'legal_name' => 'Atlantic Assurance SRL',
                'rnc' => '1-03-24680-4',
                'email' => 'servicio@atlanticassurance.com',
                'phone' => '+1-809-555-3000',
                'website' => 'https://atlanticassurance.com',
                'address' => 'Av. 27 de Febrero 506',
                'city' => 'Santiago',
                'state' => 'STI',
                'country_code' => 'DO',
                'status' => 1,
            ],
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(
                ['rnc' => $company['rnc']],
                $company
            );
        }
    }
}

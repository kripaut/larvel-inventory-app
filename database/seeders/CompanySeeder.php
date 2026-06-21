<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            'Ceramic World',
            'Premium Tiles',
            'StoneCraft',
            'WallDecor',
            'AquaFlow',
            'SanitaryPro',
            'SteelSink',
        ];

        foreach ($companies as $name) {
            Company::create(['name' => $name]);
        }
    }
}
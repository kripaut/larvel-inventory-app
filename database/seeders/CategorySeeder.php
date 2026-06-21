<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Ceramic World' => ['Tiles'],
            'Premium Tiles' => ['Tiles'],
            'StoneCraft'    => ['Artificial'],
            'WallDecor'     => ['Poster'],
            'AquaFlow'      => ['Syphon'],
            'SanitaryPro'   => ['Chair Comot', 'Wall Basin', 'Box Basin'],
            'SteelSink'     => ['Sink'],
        ];

        foreach ($map as $companyName => $categories) {
            $company = Company::where('name', $companyName)->first();

            foreach ($categories as $catName) {
                Category::create([
                    'company_id' => $company->id,
                    'name' => $catName,
                ]);
            }
        }
    }
}
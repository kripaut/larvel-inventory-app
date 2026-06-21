<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            ['Ceramic World', 'Tiles', ['300x300', '400x400']],
            ['Premium Tiles', 'Tiles', ['300x400', '600x600']],
            ['StoneCraft', 'Artificial', ['800x2400', '800x3000']],
            ['WallDecor', 'Poster', ['6x6', '8x12', '12x18', '12x24']],
            ['AquaFlow', 'Syphon', ['DT', 'PAN']],
            ['SanitaryPro', 'Chair Comot', ['Standard']],
            ['SanitaryPro', 'Wall Basin', ['Standard']],
            ['SanitaryPro', 'Box Basin', ['Standard']],
            ['SteelSink', 'Sink', ['16x18', '18x21', '18x24', '18x30']],
        ];

        foreach ($map as [$companyName, $catName, $subcats]) {
            $company = Company::where('name', $companyName)->first();
            $category = Category::where('company_id', $company->id)
                                 ->where('name', $catName)
                                 ->first();

            foreach ($subcats as $sub) {
                Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $sub,
                ]);
            }
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Classic White Tile',        'Ceramic World', 'Tiles',       '300x300', 250, 25,   32,   18, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c'],
            ['Marble Finish Tile',        'Ceramic World', 'Tiles',       '400x400', 180, 35,   45,   18, 'https://images.unsplash.com/photo-1513694203232-719a280e022f'],
            ['Wooden Texture Tile',       'Premium Tiles', 'Tiles',       '300x400', 140, 42,   55,   18, 'https://images.unsplash.com/photo-1484154218962-a197022b5858'],
            ['Luxury Vitrified Tile',     'Premium Tiles', 'Tiles',       '600x600', 95,  75,   95,   18, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85'],

            ['Royal Artificial Slab',     'StoneCraft',    'Artificial',  '800x2400', 15, 1600, 1850, 18, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85'],
            ['Premium Artificial Marble', 'StoneCraft',    'Artificial',  '800x3000', 0,  2050, 2350, 18, null],

            ['Designer Poster Tile',      'WallDecor',     'Poster',      '6x6',      150, 95,  120,  18, null],
            ['Modern Art Poster',         'WallDecor',     'Poster',      '8x12',     110, 140, 180,  18, null],
            ['Nature Theme Poster',       'WallDecor',     'Poster',      '12x18',    90,  210, 260,  18, null],
            ['Luxury Wall Poster',        'WallDecor',     'Poster',      '12x24',    75,  290, 350,  18, null],

            ['DT Syphon',                 'AquaFlow',      'Syphon',      'DT',       85,  380, 450,  18, null],
            ['PAN Syphon',                'AquaFlow',      'Syphon',      'PAN',      70,  450, 520,  18, null],

            ['Premium Chair Comot',       'SanitaryPro',   'Chair Comot', 'Standard', 40,  2800, 3200, 18, null],
            ['Wall Mounted Basin',        'SanitaryPro',   'Wall Basin',  'Standard', 55,  1500, 1800, 18, null],
            ['Square Box Basin',          'SanitaryPro',   'Box Basin',   'Standard', 35,  2100, 2500, 18, null],

            ['Compact Kitchen Sink',      'SteelSink',     'Sink',        '16x18',    4,   1200, 1500, 18, null],
            ['Classic Steel Sink',        'SteelSink',     'Sink',        '18x21',    75,  1450, 1800, 18, null],
            ['Deep Bowl Sink',            'SteelSink',     'Sink',        '18x24',    3,   1800, 2200, 18, null],
            ['Premium Double Sink',       'SteelSink',     'Sink',        '18x30',    45,  2700, 3200, 18, null],
        ];

        foreach (
            $products as [
                $name,
                $companyName,
                $catName,
                $subName,
                $stock,
                $buyPrice,
                $sellPrice,
                $gst,
                $image
            ]
        ) {
            $company = Company::where('name', $companyName)->first();

            if (!$company) {
                continue;
            }

            $category = Category::where('company_id', $company->id)
                ->where('name', $catName)
                ->first();

            if (!$category) {
                continue;
            }

            $subcategory = Subcategory::where('category_id', $category->id)
                ->where('name', $subName)
                ->first();

            if (!$subcategory) {
                continue;
            }

            Product::create([
                'name'           => $name,
                'image'          => $image,
                'company_id'     => $company->id,
                'category_id'    => $category->id,
                'subcategory_id' => $subcategory->id,
                'stock'          => $stock,
                'buy_price'      => $buyPrice,
                'sell_price'     => $sellPrice,
                'gst'            => $gst,
            ]);
        }
    }
}
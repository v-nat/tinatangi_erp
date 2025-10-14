<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'All', 'status' => 1],
            ['name' => 'Meals', 'status' => 1],
            ['name' => 'Pastries', 'status' => 1],
            ['name' => 'Beverages', 'status' => 1],
            ['name' => 'Snacks & Sides', 'status' => 1],
        ];

        DB::table('product_categories')->insert($categories);
    }
}

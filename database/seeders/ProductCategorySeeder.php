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
            ['name' => 'Rice Meal', 'status' => 1],
            ['name' => 'Omellete', 'status' => 1],
            ['name' => 'Continental', 'status' => 1],
            ['name' => 'Appetizer', 'status' => 1],
            ['name' => 'Salad', 'status' => 1],
            ['name' => 'Burger/Sandwich', 'status' => 1],
            ['name' => 'Ala Carte', 'status' => 1],
            ['name' => 'Pasta', 'status' => 1],
            ['name' => 'Soup', 'status' => 1],
            ['name' => 'Entree', 'status' => 1],
            ['name' => 'Hot Drinks', 'status' => 1],
            ['name' => 'Cold Drinks', 'status' => 1],
            ['name' => 'Coffee-Based', 'status' => 1],
            ['name' => 'Non-Coffee Based', 'status' => 1],
            ['name' => 'Hot Tea', 'status' => 1],
            ['name' => 'Iced Tea', 'status' => 1],
            ['name' => 'Non-Coffee Drinks', 'status' => 1],
            ['name' => 'Add Ons', 'status' => 1],
            ['name' => 'Tinatangi Loaf', 'status' => 1],
            ['name' => 'Pandesal', 'status' => 1],
            ['name' => 'Cakes/Pastries', 'status' => 1],
        ];

        DB::table('product_categories')->insert($categories);
    }
}

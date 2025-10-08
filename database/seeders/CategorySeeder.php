<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Raw Materials'],
            ['id' => 2, 'name' => 'Consumables'],
            ['id' => 3, 'name' => 'Dinning & Service'],
            ['id' => 4, 'name' => 'Packaging & Disposables'],
            ['id' => 5, 'name' => 'Equipment & Tools'],
            ['id' => 6, 'name' => 'Cleaning & Sanitation'],
            ['id' => 7, 'name' => 'Cold Storage'],
            ['id' => 8, 'name' => 'Staff Uniforms and Gear'],
        ];

        $dataToSeed = array_map(function ($category) {
            return array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $categories);

        DB::table('categories')->insertOrIgnore($dataToSeed);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCategory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Fetch Category IDs by name
        $categoryMap = ProductCategory::whereIn('name', ['Meals', 'Pastries', 'Beverages'])
            ->pluck('id', 'name')
            ->toArray();

        // 2. Define Category IDs for mapping
        $mealId = $categoryMap['Meals'] ?? 2;
        $pastryId = $categoryMap['Pastries'] ?? 3;
        $beverageId = $categoryMap['Beverages'] ?? 4;
        $snackId = $categoryMap['Snacks & Sides'] ?? $pastryId;

        $products = [];
        $timestamp = now();

        // --- 1. Breakfast & Rice Meals (Category: Meals) ---
        $riceMeals = [
            'Garlic Fried Rice'             => 35.00,
            'Ham & Egg Rice Meal'           => 95.00,
            'Sausage Rice Meal'             => 110.00,
            'Corned Beef Silog'             => 135.00,
            'Tuna Silog'                    => 125.00,
            'Scrambled Eggs with Rice'      => 80.00,
            'Sunny-side Up Eggs with Rice'  => 80.00,
        ];
        foreach ($riceMeals as $name => $price) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Breakfast & Rice Meals',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 2. Sandwiches & Toasts (Category: Pastries/Snacks) ---
        $sandwiches = [
            'Ham & Cheese Sandwich'     => 70.00,
            'Tuna Sandwich'             => 85.00,
            'Grilled Cheese Sandwich'   => 65.00,
            'BLT Sandwich'              => 95.00,
            'Egg Sandwich'              => 75.00,
            'Tomato & Cheese Toast'     => 80.00,
        ];
        foreach ($sandwiches as $name => $price) {
            $products[] = [
                'product_category_id' => $snackId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Sandwiches & Toasts',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 3. Pasta Dishes (Category: Meals) ---
        $pastaDishes = [
            'Tuna Garlic Pasta'         => 150.00,
            'Cheesy Garlic Pasta'       => 140.00,
            'Filipino-style Spaghetti'  => 160.00,
            'Aglio e Olio'              => 130.00,
        ];
        foreach ($pastaDishes as $name => $price) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Pasta Dishes',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 4. Salads & Light Meals (Category: Meals) ---
        $salads = [
            'Ham & Cheese Salad'            => 150.00,
            'Tuna Salad'                    => 145.00,
            'Garden Salad with Vinaigrette' => 120.00,
            'Lettuce Wraps with Tuna or Ham' => 160.00,
        ];
        foreach ($salads as $name => $price) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Salads & Light Meals',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 5. Filipino Comfort Food (Category: Meals) ---
        $comfortFood = [
            'Adobo-style Rice Bowl'         => 145.00,
            'Tuna Sisig Rice Bowl'          => 165.00,
            'Garlic Rice with Fried Canned Meat' => 100.00,
        ];
        foreach ($comfortFood as $name => $price) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Filipino Comfort Food',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 6. Snacks & Sides (Category: Pastries/Snacks) ---
        $sides = [
            'Cheese Sticks'                 => 80.00,
            'Garlic Bread'                  => 60.00,
            'Toasted Bread with Butter & Sugar' => 45.00,
            'Pan-fried Tuna Patties'        => 90.00,
        ];
        foreach ($sides as $name => $price) {
            $products[] = [
                'product_category_id' => $snackId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Snacks & Sides',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 7. HOT EPSPRESSO (Category: Beverages) ---
        $hotEspresso = [
            'Espresso Shot'                 => 70.00,
            'Americano (Hot)'               => 90.00,
            'Cappucino (Hot)'               => 120.00,
            'Latte (Hot)'                   => 115.00,
            'Caramel Latte (Hot)'           => 130.00,
        ];
        foreach ($hotEspresso as $name => $price) {
            $products[] = [
                'product_category_id' => $beverageId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Hot Espresso Drinks',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 8. ICED COFFEE (Category: Beverages) ---
        $icedCoffee = [
            'Iced Americano'                => 100.00,
            'Iced Latte'                    => 130.00,
            'Iced Caramel Macchiato'        => 145.00,
            'Spanish Latte'                 => 140.00,
            'Iced Kape Pina'                => 150.00,
        ];
        foreach ($icedCoffee as $name => $price) {
            $products[] = [
                'product_category_id' => $beverageId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Iced Coffee Drinks',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 9. FRAPPE DRINKS (Category: Beverages) ---
        $frappes = [
            'Mocha Frappe'                  => 160.00,
            'Caramel Frappe'                => 165.00,
            'Cookies & Cream Frappe'        => 170.00,
            'Coffee Frappe'                 => 155.00,
        ];
        foreach ($frappes as $name => $price) {
            $products[] = [
                'product_category_id' => $beverageId,
                'name' => $name,
                'base_price' => $price,
                'description' => 'Frappe Drinks',
                'image' => null,
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }


        // Insert all products into the database
        DB::table('products')->insert($products);
    }
}

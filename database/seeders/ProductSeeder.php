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
        $categoryMap = ProductCategory::whereIn('name', ['Meals', 'Pastries', 'Beverages', 'Snacks & Sides'])
            ->pluck('id', 'name')
            ->toArray();

        $mealId = $categoryMap['Meals'] ?? 2;
        $pastryId = $categoryMap['Pastries'] ?? 3;
        $beverageId = $categoryMap['Beverages'] ?? 4;
        $snackId = $categoryMap['Snacks & Sides'] ?? 5;

        $products = [];
        $timestamp = now();

        // --- 1. Breakfast & Rice Meals (Category: Meals) ---
        $riceMeals = [
            'Garlic Fried Rice'             => ['price' => 35.00, 'image' => 'img/products/garlic_fried_rice.jpg'],
            'Ham & Egg Rice Meal'           => ['price' => 95.00, 'image' => 'img/products/ham_and_egg_rice_meal.jpg'],
            'Sausage Rice Meal'             => ['price' => 110.00, 'image' => 'img/products/sausage_rice_meal.jpg'],
            'Corned Beef Silog'             => ['price' => 135.00, 'image' => 'img/products/cornedbeef_silog.jfif'],
            'Tuna Silog'                    => ['price' => 125.00, 'image' => 'img/products/tuna_silog.jpg'],
            'Scrambled Eggs with Rice'      => ['price' => 80.00, 'image' => 'img/products/scrambled_egg.jpg'],
            'Sunny-side Up Eggs with Rice'  => ['price' => 80.00, 'image' => 'img/products/sunnysideup_eggrice.jpg'],
        ];
        foreach ($riceMeals as $name => $data) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Breakfast & Rice Meals',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 2. Pasta Dishes (Category: Meals) ---
        $pastaDishes = [
            'Tuna Garlic Pasta'         => ['price' => 150.00, 'image' => 'img/products/spanish_tuna_garlic_Pasta.jfif'],
            'Cheesy Garlic Pasta'       => ['price' => 140.00, 'image' => 'img/products/cheesy_garlic_pasta.jfif'],
            'Filipino-style Spaghetti'  => ['price' => 160.00, 'image' => 'img/products/filipino_spaghetti.jfif'],
            'Aglio e Olio'              => ['price' => 130.00, 'image' => 'img/products/agolio_e_olio.jfif'],
        ];
        foreach ($pastaDishes as $name => $data) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Pasta Dishes',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 3. Salads & Light Meals (Category: Meals) ---
        $salads = [
            'Ham & Cheese Salad'            => ['price' => 150.00, 'image' => 'img/products/hamNcheese_salad.jfif'],
            'Tuna Salad'                    => ['price' => 145.00, 'image' => 'img/products/TunaSalad.jfif'],
            'Garden Salad with Vinaigrette' => ['price' => 120.00, 'image' => 'img/products/gardensalad_vinaigrette.jfif'],
            'Lettuce Wraps with Tuna or Ham' => ['price' => 160.00, 'image' => 'img/products/LettuceWrapWithTunaCheese.jfif'],
        ];
        foreach ($salads as $name => $data) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Salads & Light Meals',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 4. Filipino Comfort Food (Category: Meals) ---
        $comfortFood = [
            'Adobo-style Rice Bowl'              => ['price' => 145.00, 'image' => 'img/products/AdoboStyleRIceBowl.jfif'],
            'Tuna Sisig Rice Bowl'               => ['price' => 165.00, 'image' => 'img/products/TunaSisigRiceBowl.jfif'],
            'Garlic Rice with Fried Canned Meat' => ['price' => 100.00, 'image' => 'img/products/GarlicRIceCannedMeat.jfif'],
        ];
        foreach ($comfortFood as $name => $data) {
            $products[] = [
                'product_category_id' => $mealId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Filipino Comfort Food',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // ----------------------------------------------------------------------------------------------------------------------------------------
        // START PASTRIES/SNACKS CATEGORY
        // --- 5. Sandwiches & Toasts (Category: Snacks & Sides / Pastries) ---
        $sandwiches = [
            'Ham & Cheese Sandwich'     => ['price' => 70.00, 'image' => 'img/products/hamNchees_sandwich_.jfif'],
            'Tuna Sandwich'             => ['price' => 85.00, 'image' => 'img/products/tuna_sandwich.jfif'],
            'Grilled Cheese Sandwich'   => ['price' => 65.00, 'image' => 'img/products/grilled_sandwich.jfif'],
            'BLT Sandwich'              => ['price' => 95.00, 'image' => 'img/products/bit_sandwich.jfif'],
            'Egg Sandwich'              => ['price' => 75.00, 'image' => 'img/products/egg_sandwich.jfif'],
            'Tomato & Cheese Toast'     => ['price' => 80.00, 'image' => 'img/products/tomatoNcheese_Sandwich.jfif'],
        ];
        foreach ($sandwiches as $name => $data) {
            $products[] = [
                'product_category_id' => $pastryId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Sandwiches & Toasts',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 6. Snacks & Sides (Category: Snacks & Sides / Pastries) ---
        $sides = [
            'Cheese Sticks'                     => ['price' => 80.00, 'image' => 'img/products/Cheese_stick.jfif'],
            'Garlic Bread'                      => ['price' => 60.00, 'image' => 'img/products/Garlic_bread.jfif'],
            'Toasted Bread with Butter & Sugar' => ['price' => 45.00, 'image' => 'img/products/ToastedBreadButterSugar.jfif'],
            'Pan-fried Tuna Patties'            => ['price' => 90.00, 'image' => 'img/products/PanfriedTunaPatties.jfif'],
        ];
        foreach ($sides as $name => $data) {
            $products[] = [
                'product_category_id' => $snackId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Snacks & Sides',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }
        // END PASTRIES/SNACKS CATEGORY
        // ----------------------------------------------------------------------------------------------------------------------------------------

        // --- 7. HOT EPSPRESSO (Category: Beverages) ---
        $hotEspresso = [
            'Espresso Shot'       => ['price' => 70.00, 'image' => 'img/products/EspressoShot.jfif'],
            'Americano (Hot)'     => ['price' => 90.00, 'image' => 'img/products/americano.jfif'],
            'Cappucino (Hot)'     => ['price' => 120.00, 'image' => 'img/products/Cappucino.jfif'],
            'Latte (Hot)'         => ['price' => 115.00, 'image' => 'img/products/Latte.jfif'],
            'Caramel Latte (Hot)' => ['price' => 130.00, 'image' => 'img/products/CaramelLatte.jfif'],
        ];
        foreach ($hotEspresso as $name => $data) {
            $products[] = [
                'product_category_id' => $beverageId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Hot Espresso Drinks',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 8. ICED COFFEE (Category: Beverages) ---
        $icedCoffee = [
            'Iced Americano'          => ['price' => 100.00, 'image' => 'img/products/IcedAmericano.jfif'],
            'Iced Latte'              => ['price' => 130.00, 'image' => 'img/products/IcedLatte.jfif'],
            'Iced Caramel Macchiato'  => ['price' => 145.00, 'image' => 'img/products/IcedCaramelMacchiato.jfif'],
            'Spanish Latte'           => ['price' => 140.00, 'image' => 'img/products/SpanishLatte.jfif'],
            'Iced Kape Pina'          => ['price' => 150.00, 'image' => 'img/products/IcedKapePina.jfif'],
        ];
        foreach ($icedCoffee as $name => $data) {
            $products[] = [
                'product_category_id' => $beverageId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Iced Coffee Drinks',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // --- 9. FRAPPE DRINKS (Category: Beverages) ---
        $frappes = [
            'Mocha Frappe'          => ['price' => 160.00, 'image' => 'img/products/MochaFrappe.jfif'],
            'Caramel Frappe'        => ['price' => 165.00, 'image' => 'img/products/CaramelFrappe.jfif'],
            'Cookies & Cream Frappe'=> ['price' => 170.00, 'image' => 'img/products/CookiesAndCreamfrappe.jfif'],
            'Coffee Frappe'         => ['price' => 155.00, 'image' => 'img/products/CoffeeFrappe.jfif'],
        ];
        foreach ($frappes as $name => $data) {
            $products[] = [
                'product_category_id' => $beverageId,
                'name' => $name,
                'base_price' => $data['price'],
                'description' => 'Frappe Drinks',
                'image' => $data['image'],
                'status' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Insert all products into the database
        DB::table('products')->insert($products);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category IDs:
        // 1: Raw Materials, 2: Consumables, 3: Dinning & Service, 4: Packaging & Disposables
        // 5: Equipment & Tools, 6: Cleaning & Sanitation, 7: Cold Storage, 8: Staff Uniforms and Gear

        // Unit IDs:
        // 1: KG, 2: LITER, 3: BOTTLE, 4: BOX, 5: PCS, 6: SLEEVE, 7: BUNDLE, 8: CASE, 9: JAR,
        // 10: TUB, 11: SET, 12: UNIT, 13: G, 14: ML, 15: BAG, 16: SACHET, 17: ROLL, 18: PACK

        // Inventory Location IDs:
        // 1: Bakery Department, 2: Coffee Department, 3: Kitchen Department,
        // 4: Equipment Room, 5: Storage Room, 6: Cleaning Supplies Room, 7: Staff Area

        $items = [];
        $idCounter = 1;

        $createItem = function (int $id, string $name, int $category_id, int $unit_id, float $unit_price, int $location_id) {
            return [
                'id' => $id,
                'name' => $name,
                'category_id' => $category_id,
                'unit_id' => $unit_id,
                'unit_price' => $unit_price,
                'inventory_location_id' => $location_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        };

        // --- 1. RAW MATERIALS (category_id: 1) ---
        $rawMaterialsData = [
            // Bakery Ingredients (Location ID: 1)
            'All-purpose flour' => [1, 85.00, 1],
            'Baking powder' => [1, 220.00, 1],
            'Baking soda' => [1, 180.00, 1],
            'Brown Sugar' => [1, 70.00, 1],
            'Butter' => [10, 270.00, 1],
            'Cake flour' => [1, 110.00, 1],
            'Chocolate chips' => [18, 200.00, 1],
            'Cocoa powder' => [1, 480.00, 1],
            'Condensed Milk' => [9, 65.00, 1],
            'Eggs' => [5, 7.00, 1],
            'Evaporated Milk' => [9, 55.00, 1],
            'Food coloring' => [3, 80.00, 1],
            'Fresh Milk' => [2, 95.00, 1],
            'Margarine' => [10, 150.00, 1],
            'Powdered Sugar' => [1, 150.00, 1],
            'Vanilla extract' => [3, 120.00, 1],
            'White Sugar' => [1, 65.00, 1],
            'Yeast' => [18, 30.00, 1],

            // Coffee Ingredients (Location ID: 2)
            'Almond Milk' => [3, 120.00, 2],
            'Caramel Syrup' => [3, 450.00, 2],
            'Chocolate Sauce' => [3, 400.00, 2],
            'Coffee Beans (Arabica)' => [1, 750.00, 2],
            'Coffee Beans (Blends)' => [1, 600.00, 2],
            'Coffee Beans (Robusta)' => [1, 420.00, 2],
            'Cream' => [2, 320.00, 2],
            'Espresso pods' => [18, 250.00, 2],
            'Ground coffee' => [1, 550.00, 2],
            'Hazelnut Syrup' => [3, 480.00, 2],
            'Mocha Syrup' => [3, 450.00, 2],
            'Muscovado Sugar' => [1, 80.00, 2],
            'Oat Milk' => [3, 150.00, 2],
            'Soy Milk' => [3, 90.00, 2],
            'Vanilla Syrup' => [3, 450.00, 2],
            'Whipped Cream' => [10, 280.00, 2],

            // Kitchen/General Prep Ingredients (Location ID: 3)
            'Bread Rolls' => [5, 12.00, 3],
            'Canned Corned Beef' => [9, 85.00, 3],
            'Canned Tuna' => [9, 50.00, 3],
            'Cheese' => [10, 350.00, 3],
            'Chili Seasoning' => [1, 450.00, 3],
            'Cooking oil' => [2, 180.00, 3],
            'Flakes Seasoning' => [1, 400.00, 3],
            'Garlic' => [1, 180.00, 3],
            'Ham' => [1, 300.00, 3],
            'Herbs Seasoning' => [1, 650.00, 3],
            'Lettuce' => [5, 70.00, 3],
            'Loaf Bread' => [5, 80.00, 3],
            'Onion' => [1, 120.00, 3],
            'Pasta' => [1, 90.00, 3],
            'Pepper Seasoning' => [1, 450.00, 3],
            'Rice' => [1, 55.00, 3],
            'Salt' => [1, 30.00, 3],
            'Sandwich Bread' => [5, 90.00, 3],
            'Sausages' => [1, 320.00, 3],
            'Soy sauce' => [3, 70.00, 3],
            'Tomatoes' => [1, 110.00, 3],
            'Vinegar' => [3, 50.00, 3],

            // Shared/Bulk (Location ID: 5)
            'Ice' => [15, 35.00, 5],
        ];

        foreach ($rawMaterialsData as $name => $data) {
            $items[] = $createItem($idCounter++, $name, 1, $data[0], $data[1], $data[2]);
        }

        // --- 2. CONSUMABLES (category_id: 2) ---
        // Stored in the Storage Room (Location ID: 5)
        $category_id = 2;
        $consumables = [
            ['name' => 'Coffee Filters', 'unit_id' => 18, 'unit_price' => 180.00, 'location_id' => 5],
            ['name' => 'Cup Lids', 'unit_id' => 6, 'unit_price' => 110.00, 'location_id' => 5],
            ['name' => 'Napkins', 'unit_id' => 18, 'unit_price' => 250.00, 'location_id' => 5],
            ['name' => 'Paper Cups (large)', 'unit_id' => 6, 'unit_price' => 180.00, 'location_id' => 5],
            ['name' => 'Paper Cups (medium)', 'unit_id' => 6, 'unit_price' => 160.00, 'location_id' => 5],
            ['name' => 'Paper Cups (small)', 'unit_id' => 6, 'unit_price' => 140.00, 'location_id' => 5],
            ['name' => 'Stir Sticks', 'unit_id' => 18, 'unit_price' => 150.00, 'location_id' => 5],
            ['name' => 'Take-out Bags', 'unit_id' => 18, 'unit_price' => 120.00, 'location_id' => 5],
        ];

        foreach ($consumables as $item) {
            $items[] = $createItem($idCounter++, $item['name'], $category_id, $item['unit_id'], $item['unit_price'], $item['location_id']);
        }

        // --- 3. DINING & SERVICE (category_id: 3) ---
        // Stored in the Storage Room (Location ID: 5)
        $category_id = 3;
        $diningService = [
            ['name' => 'Bowls', 'unit_id' => 5, 'unit_price' => 180.00, 'location_id' => 5],
            ['name' => 'Condiment containers (for sugar, salt, pepper)', 'unit_id' => 11, 'unit_price' => 350.00, 'location_id' => 5],
            ['name' => 'Glasses & mugs', 'unit_id' => 5, 'unit_price' => 150.00, 'location_id' => 5],
            ['name' => 'Menu holders', 'unit_id' => 5, 'unit_price' => 200.00, 'location_id' => 5],
            ['name' => 'Pitchers', 'unit_id' => 5, 'unit_price' => 450.00, 'location_id' => 5],
            ['name' => 'Plates (ceramic or disposable)', 'unit_id' => 5, 'unit_price' => 180.00, 'location_id' => 5],
            ['name' => 'Serving trays', 'unit_id' => 5, 'unit_price' => 500.00, 'location_id' => 5],
            ['name' => 'Table napkins', 'unit_id' => 5, 'unit_price' => 80.00, 'location_id' => 5],
            ['name' => 'Tablecloths', 'unit_id' => 5, 'unit_price' => 350.00, 'location_id' => 5],
            ['name' => 'Utensils (forks, spoons, knives)', 'unit_id' => 11, 'unit_price' => 800.00, 'location_id' => 5],
        ];

        foreach ($diningService as $item) {
            $items[] = $createItem($idCounter++, $item['name'], $category_id, $item['unit_id'], $item['unit_price'], $item['location_id']);
        }

        // --- 4. PACKAGING & DISPOSABLES (category_id: 4) ---
        // Stored in the Storage Room (Location ID: 5)
        $category_id = 4;
        $packaging = [
            ['name' => 'Bread bags', 'unit_id' => 4, 'unit_price' => 220.00, 'location_id' => 5],
            ['name' => 'Cake boxes', 'unit_id' => 4, 'unit_price' => 350.00, 'location_id' => 5],
            ['name' => 'Coffee sleeves', 'unit_id' => 6, 'unit_price' => 180.00, 'location_id' => 5],
            ['name' => 'Cup carriers', 'unit_id' => 4, 'unit_price' => 280.00, 'location_id' => 5],
            ['name' => 'Eco-friendly containers', 'unit_id' => 4, 'unit_price' => 550.00, 'location_id' => 5],
            ['name' => 'Paper bags', 'unit_id' => 4, 'unit_price' => 300.00, 'location_id' => 5],
            ['name' => 'Plastic containers (for take-out meals)', 'unit_id' => 4, 'unit_price' => 480.00, 'location_id' => 5],
        ];

        foreach ($packaging as $item) {
            $items[] = $createItem($idCounter++, $item['name'], $category_id, $item['unit_id'], $item['unit_price'], $item['location_id']);
        }

        // --- 5. EQUIPMENT & TOOLS (category_id: 5) ---
        // Stored in the Equipment Room (Location ID: 4)
        $category_id = 5;
        $equipment = [
            ['name' => 'Blender', 'unit_id' => 12, 'unit_price' => 2500.00, 'location_id' => 4],
            ['name' => 'Coffee machine', 'unit_id' => 12, 'unit_price' => 35000.00, 'location_id' => 4],
            ['name' => 'Cooking pot & pan', 'unit_id' => 11, 'unit_price' => 3000.00, 'location_id' => 4],
            ['name' => 'Freezer', 'unit_id' => 12, 'unit_price' => 25000.00, 'location_id' => 4],
            ['name' => 'Measuring tool', 'unit_id' => 11, 'unit_price' => 350.00, 'location_id' => 4],
            ['name' => 'Oven', 'unit_id' => 12, 'unit_price' => 22000.00, 'location_id' => 4],
            ['name' => 'Refrigerator', 'unit_id' => 12, 'unit_price' => 28000.00, 'location_id' => 4],
            ['name' => 'Toaster', 'unit_id' => 12, 'unit_price' => 1500.00, 'location_id' => 4],
            ['name' => 'Utensil', 'unit_id' => 5, 'unit_price' => 120.00, 'location_id' => 4],
        ];

        foreach ($equipment as $item) {
            $items[] = $createItem($idCounter++, $item['name'], $category_id, $item['unit_id'], $item['unit_price'], $item['location_id']);
        }

        // --- 6. CLEANING & SANITATION (category_id: 6) ---
        // Stored in the Cleaning Supplies Room (Location ID: 6) or Staff Area (Location ID: 7)
        $category_id = 6;
        $cleaning = [
            ['name' => 'Aprons', 'unit_id' => 5, 'unit_price' => 180.00, 'location_id' => 7], // Reusable/Staff
            ['name' => 'Blades', 'unit_id' => 18, 'unit_price' => 250.00, 'location_id' => 6],
            ['name' => 'Broom & dustpan', 'unit_id' => 11, 'unit_price' => 450.00, 'location_id' => 6],
            ['name' => 'Cleaning cloths', 'unit_id' => 18, 'unit_price' => 150.00, 'location_id' => 6],
            ['name' => 'Cleaning rags', 'unit_id' => 18, 'unit_price' => 280.00, 'location_id' => 6],
            ['name' => 'Disinfectant sprays', 'unit_id' => 3, 'unit_price' => 180.00, 'location_id' => 6],
            ['name' => 'Dishwashing gloves', 'unit_id' => 5, 'unit_price' => 70.00, 'location_id' => 6],
            ['name' => 'Dishwashing liquid', 'unit_id' => 3, 'unit_price' => 150.00, 'location_id' => 6],
            ['name' => 'Face masks', 'unit_id' => 18, 'unit_price' => 120.00, 'location_id' => 7], // Staff Gear
            ['name' => 'Foil & cling wrap', 'unit_id' => 17, 'unit_price' => 250.00, 'location_id' => 3], // Used in Kitchen
            ['name' => 'Gloves', 'unit_id' => 18, 'unit_price' => 350.00, 'location_id' => 7], // Staff Gear
            ['name' => 'Hairnets', 'unit_id' => 18, 'unit_price' => 180.00, 'location_id' => 7], // Staff Gear
            ['name' => 'Mop & bucket', 'unit_id' => 11, 'unit_price' => 800.00, 'location_id' => 6],
            ['name' => 'Sanitizers', 'unit_id' => 3, 'unit_price' => 300.00, 'location_id' => 6],
            ['name' => 'Sponges', 'unit_id' => 18, 'unit_price' => 100.00, 'location_id' => 6],
            ['name' => 'Trash bags', 'unit_id' => 18, 'unit_price' => 150.00, 'location_id' => 6],
            ['name' => 'Trash bins', 'unit_id' => 5, 'unit_price' => 400.00, 'location_id' => 6],
        ];

        foreach ($cleaning as $item) {
            $items[] = $createItem($idCounter++, $item['name'], $category_id, $item['unit_id'], $item['unit_price'], $item['location_id']);
        }

        // --- 7. COLD STORAGE (category_id: 7) ---
        // Stored in the Storage Room (Location ID: 5)
        $category_id = 7;
        $coldStorage = [
            ['name' => 'Freezer-safe containers', 'unit_id' => 11, 'unit_price' => 600.00, 'location_id' => 5],
            ['name' => 'Ice packs', 'unit_id' => 5, 'unit_price' => 150.00, 'location_id' => 5],
            ['name' => 'Thermometers (for fridge/freezer monitoring)', 'unit_id' => 5, 'unit_price' => 450.00, 'location_id' => 5],
        ];

        foreach ($coldStorage as $item) {
            $items[] = $createItem($idCounter++, $item['name'], $category_id, $item['unit_id'], $item['unit_price'], $item['location_id']);
        }

        // --- 8. STAFF UNIFORMS AND GEAR (category_id: 8) ---
        // Stored in the Staff Area (Location ID: 7)
        $category_id = 8;
        $uniforms = [
            ['name' => 'Barista aprons', 'unit_id' => 5, 'unit_price' => 300.00, 'location_id' => 7],
            ['name' => 'Chef hats', 'unit_id' => 5, 'unit_price' => 150.00, 'location_id' => 7],
            ['name' => 'Name tags', 'unit_id' => 5, 'unit_price' => 50.00, 'location_id' => 7],
            ['name' => 'Safety shoes', 'unit_id' => 5, 'unit_price' => 1800.00, 'location_id' => 7],
        ];

        foreach ($uniforms as $item) {
            $items[] = $createItem($idCounter++, $item['name'], $category_id, $item['unit_id'], $item['unit_price'], $item['location_id']);
        }

        DB::table('items')->insert($items);
    }
}

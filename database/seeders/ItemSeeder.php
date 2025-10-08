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

        $items = [];
        $idCounter = 1;

        // --- 1. RAW MATERIALS (category_id: 1) ---
        // Array structure: ['name' => ['unit_id', 'unit_price']]
        // Items are sorted alphabetically by name.
        $rawMaterialsData = [
            'All-purpose flour' => [1, 85.00],
            'Almond Milk' => [3, 120.00],
            'Baking powder' => [1, 220.00],
            'Baking soda' => [1, 180.00],
            'Bread Rolls' => [5, 12.00], // Per piece
            'Brown Sugar' => [1, 70.00],
            'Butter' => [10, 270.00], // Per 1kg tub/pack
            'Cake flour' => [1, 110.00],
            'Canned Corned Beef' => [9, 85.00], // Per can/jar
            'Canned Tuna' => [9, 50.00], // Per can/jar
            'Caramel Syrup' => [3, 450.00],
            'Cheese' => [10, 350.00], // Per 1kg tub/block
            'Chili Seasoning' => [1, 450.00],
            'Chocolate Sauce' => [3, 400.00],
            'Chocolate chips' => [18, 200.00], // Per pack
            'Cocoa powder' => [1, 480.00],
            'Coffee Beans (Arabica)' => [1, 750.00],
            'Coffee Beans (Blends)' => [1, 600.00],
            'Coffee Beans (Robusta)' => [1, 420.00],
            'Condensed Milk' => [9, 65.00], // Per jar/can
            'Cooking oil' => [2, 180.00], // Per liter (commercial pack)
            'Cream' => [2, 320.00], // Per liter
            'Eggs' => [5, 7.00], // Per piece
            'Espresso pods' => [18, 250.00], // Per pack (e.g., box of 10)
            'Evaporated Milk' => [9, 55.00], // Per jar/can
            'Flakes Seasoning' => [1, 400.00],
            'Food coloring' => [3, 80.00],
            'Fresh Milk' => [2, 95.00],
            'Garlic' => [1, 180.00],
            'Ground coffee' => [1, 550.00],
            'Ham' => [1, 300.00],
            'Hazelnut Syrup' => [3, 480.00],
            'Herbs Seasoning' => [1, 650.00],
            'Ice' => [15, 35.00], // Per bag of ice
            'Lettuce' => [5, 70.00], // Per head
            'Loaf Bread' => [5, 80.00], // Per loaf
            'Margarine' => [10, 150.00], // Per 1kg tub/pack
            'Mocha Syrup' => [3, 450.00],
            'Muscovado Sugar' => [1, 80.00],
            'Oat Milk' => [3, 150.00],
            'Onion' => [1, 120.00],
            'Pasta' => [1, 90.00],
            'Pepper Seasoning' => [1, 450.00],
            'Powdered Sugar' => [1, 150.00],
            'Rice' => [1, 55.00],
            'Salt' => [1, 30.00],
            'Sandwich Bread' => [5, 90.00], // Per loaf
            'Sausages' => [1, 320.00],
            'Soy Milk' => [3, 90.00],
            'Soy sauce' => [3, 70.00],
            'Tomatoes' => [1, 110.00],
            'Vanilla Syrup' => [3, 450.00],
            'Vanilla extract' => [3, 120.00],
            'Vinegar' => [3, 50.00],
            'Whipped Cream' => [10, 280.00], // Per tub
            'White Sugar' => [1, 65.00],
            'Yeast' => [18, 30.00], // Per sachet/pack
        ];

        foreach ($rawMaterialsData as $name => $data) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $name,
                'category_id' => 1,
                'unit_id' => $data[0],
                'unit_price' => $data[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // --- 2. CONSUMABLES (category_id: 2) ---
        // Common Unit: SLEEVE (6) or PACK (18)
        $category_id = 2;
        $consumables = [
            ['name' => 'Coffee Filters', 'unit_id' => 18, 'unit_price' => 180.00], // Per pack of 100/200
            ['name' => 'Cup Lids', 'unit_id' => 6, 'unit_price' => 110.00], // Per sleeve of 50
            ['name' => 'Napkins', 'unit_id' => 18, 'unit_price' => 250.00], // Per pack (e.g., 1000 pcs)
            ['name' => 'Paper Cups (large)', 'unit_id' => 6, 'unit_price' => 180.00], // Per sleeve of 50
            ['name' => 'Paper Cups (medium)', 'unit_id' => 6, 'unit_price' => 160.00], // Per sleeve of 50
            ['name' => 'Paper Cups (small)', 'unit_id' => 6, 'unit_price' => 140.00], // Per sleeve of 50
            ['name' => 'Stir Sticks', 'unit_id' => 18, 'unit_price' => 150.00], // Per pack (e.g., 1000 pcs)
            ['name' => 'Take-out Bags', 'unit_id' => 18, 'unit_price' => 120.00], // Per pack (e.g., 100 pcs)
        ];

        foreach ($consumables as $item) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $item['name'],
                'category_id' => $category_id,
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // --- 3. DINING & SERVICE (category_id: 3) ---
        // Common Unit: PCS (5) or SET (11)
        $category_id = 3;
        $diningService = [
            ['name' => 'Bowls', 'unit_id' => 5, 'unit_price' => 180.00], // Per ceramic piece
            ['name' => 'Condiment containers (for sugar, salt, pepper)', 'unit_id' => 11, 'unit_price' => 350.00], // Per set of 3
            ['name' => 'Glasses & mugs', 'unit_id' => 5, 'unit_price' => 150.00], // Per ceramic piece
            ['name' => 'Menu holders', 'unit_id' => 5, 'unit_price' => 200.00], // Per piece
            ['name' => 'Pitchers', 'unit_id' => 5, 'unit_price' => 450.00], // Per piece
            ['name' => 'Plates (ceramic or disposable)', 'unit_id' => 5, 'unit_price' => 180.00], // Per ceramic piece
            ['name' => 'Serving trays', 'unit_id' => 5, 'unit_price' => 500.00], // Per piece
            ['name' => 'Table napkins', 'unit_id' => 5, 'unit_price' => 80.00], // Per reusable cloth napkin
            ['name' => 'Tablecloths', 'unit_id' => 5, 'unit_price' => 350.00], // Per piece
            ['name' => 'Utensils (forks, spoons, knives)', 'unit_id' => 11, 'unit_price' => 800.00], // Per set of 12
        ];

        foreach ($diningService as $item) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $item['name'],
                'category_id' => $category_id,
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // --- 4. PACKAGING & DISPOSABLES (category_id: 4) ---
        // Common Unit: BOX (4) or SLEEVE (6)
        $category_id = 4;
        $packaging = [
            ['name' => 'Bread bags', 'unit_id' => 4, 'unit_price' => 220.00], // Per box of 100
            ['name' => 'Cake boxes', 'unit_id' => 4, 'unit_price' => 350.00], // Per box of 50
            ['name' => 'Coffee sleeves', 'unit_id' => 6, 'unit_price' => 180.00], // Per sleeve of 100
            ['name' => 'Cup carriers', 'unit_id' => 4, 'unit_price' => 280.00], // Per box of 50
            ['name' => 'Eco-friendly containers', 'unit_id' => 4, 'unit_price' => 550.00], // Per box of 50
            ['name' => 'Paper bags', 'unit_id' => 4, 'unit_price' => 300.00], // Per box of 100
            ['name' => 'Plastic containers (for take-out meals)', 'unit_id' => 4, 'unit_price' => 480.00], // Per box of 100
        ];

        foreach ($packaging as $item) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $item['name'],
                'category_id' => $category_id,
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // --- 5. EQUIPMENT & TOOLS (category_id: 5) ---
        // Common Unit: UNIT (12) or SET (11)
        $category_id = 5;
        $equipment = [
            ['name' => 'Blender', 'unit_id' => 12, 'unit_price' => 2500.00],
            ['name' => 'Coffee machine', 'unit_id' => 12, 'unit_price' => 35000.00],
            ['name' => 'Cooking pot & pan', 'unit_id' => 11, 'unit_price' => 3000.00], // Per set
            ['name' => 'Freezer', 'unit_id' => 12, 'unit_price' => 25000.00],
            ['name' => 'Measuring tool', 'unit_id' => 11, 'unit_price' => 350.00], // Per set of cups/spoons
            ['name' => 'Oven', 'unit_id' => 12, 'unit_price' => 22000.00],
            ['name' => 'Refrigerator', 'unit_id' => 12, 'unit_price' => 28000.00],
            ['name' => 'Toaster', 'unit_id' => 12, 'unit_price' => 1500.00],
            ['name' => 'Utensil', 'unit_id' => 5, 'unit_price' => 120.00], // Per piece (e.g., individual spatula, whisk)
        ];

        foreach ($equipment as $item) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $item['name'],
                'category_id' => $category_id,
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // --- 6. CLEANING & SANITATION (category_id: 6) ---
        // Common Unit: BOTTLE (3), PACK (18), ROLL (17), SET (11)
        $category_id = 6;
        $cleaning = [
            ['name' => 'Aprons', 'unit_id' => 5, 'unit_price' => 180.00], // Per piece
            ['name' => 'Blades', 'unit_id' => 18, 'unit_price' => 250.00], // Per pack (e.g., utility knife blades)
            ['name' => 'Broom & dustpan', 'unit_id' => 11, 'unit_price' => 450.00], // Per set
            ['name' => 'Cleaning cloths', 'unit_id' => 18, 'unit_price' => 150.00], // Per pack of 10
            ['name' => 'Cleaning rags', 'unit_id' => 18, 'unit_price' => 280.00], // Per bulk pack
            ['name' => 'Disinfectant sprays', 'unit_id' => 3, 'unit_price' => 180.00], // Per bottle
            ['name' => 'Dishwashing gloves', 'unit_id' => 5, 'unit_price' => 70.00], // Per pair (PCS)
            ['name' => 'Dishwashing liquid', 'unit_id' => 3, 'unit_price' => 150.00], // Per 1L bottle
            ['name' => 'Face masks', 'unit_id' => 18, 'unit_price' => 120.00], // Per pack of 50
            ['name' => 'Foil & cling wrap', 'unit_id' => 17, 'unit_price' => 250.00], // Per roll
            ['name' => 'Gloves', 'unit_id' => 18, 'unit_price' => 350.00], // Per box of 100 disposable
            ['name' => 'Hairnets', 'unit_id' => 18, 'unit_price' => 180.00], // Per pack of 100
            ['name' => 'Mop & bucket', 'unit_id' => 11, 'unit_price' => 800.00], // Per set
            ['name' => 'Sanitizers', 'unit_id' => 3, 'unit_price' => 300.00], // Per 1L bottle
            ['name' => 'Sponges', 'unit_id' => 18, 'unit_price' => 100.00], // Per pack of 10
            ['name' => 'Trash bags', 'unit_id' => 18, 'unit_price' => 150.00], // Per pack of 50
            ['name' => 'Trash bins', 'unit_id' => 5, 'unit_price' => 400.00], // Per piece
        ];

        foreach ($cleaning as $item) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $item['name'],
                'category_id' => $category_id,
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // --- 7. COLD STORAGE (category_id: 7) ---
        // Common Unit: SET (11), PCS (5)
        $category_id = 7;
        $coldStorage = [
            ['name' => 'Freezer-safe containers', 'unit_id' => 11, 'unit_price' => 600.00], // Per set of 5
            ['name' => 'Ice packs', 'unit_id' => 5, 'unit_price' => 150.00], // Per piece
            ['name' => 'Thermometers (for fridge/freezer monitoring)', 'unit_id' => 5, 'unit_price' => 450.00], // Per piece
        ];

        foreach ($coldStorage as $item) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $item['name'],
                'category_id' => $category_id,
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // --- 8. STAFF UNIFORMS AND GEAR (category_id: 8) ---
        // Common Unit: PCS (5)
        $category_id = 8;
        $uniforms = [
            ['name' => 'Barista aprons', 'unit_id' => 5, 'unit_price' => 300.00], // Per piece
            ['name' => 'Chef hats', 'unit_id' => 5, 'unit_price' => 150.00], // Per piece
            ['name' => 'Name tags', 'unit_id' => 5, 'unit_price' => 50.00], // Per piece
            ['name' => 'Safety shoes', 'unit_id' => 5, 'unit_price' => 1800.00], // Per piece (pair)
        ];

        foreach ($uniforms as $item) {
            $items[] = [
                'id' => $idCounter++,
                'name' => $item['name'],
                'category_id' => $category_id,
                'unit_id' => $item['unit_id'],
                'unit_price' => $item['unit_price'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('items')->insert($items);
    }
}

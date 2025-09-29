<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // --- Category ID 1: Raw Materials ---
            ['category_id' => 1, 'name' => 'Coffee Beans - Espresso Blend', 'unit' => 'KG', 'unit_price' => 750.00], // Example price per KG
            ['category_id' => 1, 'name' => 'Coffee Beans - Drip Roast', 'unit' => 'KG', 'unit_price' => 680.00],
            ['category_id' => 1, 'name' => 'Milk - Whole', 'unit' => 'LITER', 'unit_price' => 85.50], // Example price per Liter
            ['category_id' => 1, 'name' => 'Milk - Oat Alternative', 'unit' => 'LITER', 'unit_price' => 150.00],
            ['category_id' => 1, 'name' => 'Syrup - Vanilla', 'unit' => 'BOTTLE', 'unit_price' => 420.00],
            ['category_id' => 1, 'name' => 'Tea Bags - Black', 'unit' => 'BOX', 'unit_price' => 280.00], // Price per Box of bags
            ['category_id' => 1, 'name' => 'Sugar Packets', 'unit' => 'BOX', 'unit_price' => 950.00], // Price per large Box of packets
            ['category_id' => 1, 'name' => 'Pastries/Baked Goods', 'unit' => 'PCS', 'unit_price' => 45.00],

            // --- Category ID 2: Disposables ---
            ['category_id' => 2, 'name' => 'Hot Cups - 12oz', 'unit' => 'SLEEVE', 'unit_price' => 250.00], // Price per Sleeve
            ['category_id' => 2, 'name' => 'Lids - 12oz', 'unit' => 'SLEEVE', 'unit_price' => 180.00],
            ['category_id' => 2, 'name' => 'Cup Sleeves', 'unit' => 'BUNDLE', 'unit_price' => 350.00],
            ['category_id' => 2, 'name' => 'Cold Cups - 16oz', 'unit' => 'SLEEVE', 'unit_price' => 320.00],
            ['category_id' => 2, 'name' => 'Straws', 'unit' => 'BOX', 'unit_price' => 450.00],
            ['category_id' => 2, 'name' => 'Napkins', 'unit' => 'CASE', 'unit_price' => 1200.00],
            ['category_id' => 2, 'name' => 'Stirrers', 'unit' => 'BOX', 'unit_price' => 210.00],
            ['category_id' => 2, 'name' => 'Takeout Drink Carriers', 'unit' => 'BUNDLE', 'unit_price' => 550.00],

            // --- Category ID 3: Barista Tools & Accessories ---
            ['category_id' => 3, 'name' => 'Frothing Pitchers', 'unit' => 'PCS', 'unit_price' => 450.00], // Price per Piece
            ['category_id' => 3, 'name' => 'Espresso Tamper', 'unit' => 'PCS', 'unit_price' => 1200.00],
            ['category_id' => 3, 'name' => 'Knock Box', 'unit' => 'PCS', 'unit_price' => 980.00],
            ['category_id' => 3, 'name' => 'Digital Scale', 'unit' => 'PCS', 'unit_price' => 850.00],
            ['category_id' => 3, 'name' => 'Shot Glasses/Jiggers', 'unit' => 'PCS', 'unit_price' => 150.00],
            ['category_id' => 3, 'name' => 'Syrup Pumps', 'unit' => 'PCS', 'unit_price' => 80.00],

            // --- Category ID 4: Equipment ---
            ['category_id' => 4, 'name' => 'Commercial Espresso Machine', 'unit' => 'UNIT', 'unit_price' => 150000.00], // Price per Unit
            ['category_id' => 4, 'name' => 'Espresso Grinder', 'unit' => 'UNIT', 'unit_price' => 35000.00],
            ['category_id' => 4, 'name' => 'Drip Coffee Brewer', 'unit' => 'UNIT', 'unit_price' => 15000.00],
            ['category_id' => 4, 'name' => 'Water Filtration System', 'unit' => 'UNIT', 'unit_price' => 8000.00],
            ['category_id' => 4, 'name' => 'Commercial Refrigerator', 'unit' => 'UNIT', 'unit_price' => 45000.00],
            ['category_id' => 4, 'name' => 'POS Terminal', 'unit' => 'UNIT', 'unit_price' => 25000.00],

            // --- Category ID 5: Cleaning & Sanitation ---
            ['category_id' => 5, 'name' => 'Espresso Machine Backflush Detergent', 'unit' => 'JAR', 'unit_price' => 600.00], // Price per Jar
            ['category_id' => 5, 'name' => 'Grinder Cleaning Tablets', 'unit' => 'JAR', 'unit_price' => 750.00],
            ['category_id' => 5, 'name' => 'Dish Soap - Commercial Grade', 'unit' => 'LITER', 'unit_price' => 120.00],
            ['category_id' => 5, 'name' => 'Sanitizing Wipes', 'unit' => 'TUB', 'unit_price' => 300.00],
            ['category_id' => 5, 'name' => 'Floor Mop and Bucket', 'unit' => 'SET', 'unit_price' => 850.00],
            ['category_id' => 5, 'name' => 'Hand Soap', 'unit' => 'BOTTLE', 'unit_price' => 90.00],

            // --- Category ID 6: Furniture & Decor ---
            ['category_id' => 6, 'name' => 'Customer Tables', 'unit' => 'UNIT', 'unit_price' => 3500.00], // Price per Unit
            ['category_id' => 6, 'name' => 'Dining Chairs', 'unit' => 'PCS', 'unit_price' => 950.00],
            ['category_id' => 6, 'name' => 'Wall Shelving Unit', 'unit' => 'UNIT', 'unit_price' => 2800.00],
            ['category_id' => 6, 'name' => 'Ceramic Coffee Mugs', 'unit' => 'PCS', 'unit_price' => 80.00],
            ['category_id' => 6, 'name' => 'Glassware for Iced Drinks', 'unit' => 'PCS', 'unit_price' => 75.00],
            ['category_id' => 6, 'name' => 'Menu Display Board', 'unit' => 'UNIT', 'unit_price' => 1500.00],
        ];
        foreach ($items as $item) {
            Item::create($item);
        }
    }
}

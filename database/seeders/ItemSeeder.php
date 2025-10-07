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
            ['category_id' => 1, 'name' => 'Coffee Beans - Espresso Blend', 'unit_id' => 1, 'unit_price' => 750.00], // Example price per KG
            ['category_id' => 1, 'name' => 'Coffee Beans - Drip Roast', 'unit_id' => 1, 'unit_price' => 680.00],
            ['category_id' => 1, 'name' => 'Milk - Whole', 'unit_id' => 2, 'unit_price' => 85.50], // Example price per Liter
            ['category_id' => 1, 'name' => 'Milk - Oat Alternative', 'unit_id' => 2, 'unit_price' => 150.00],
            ['category_id' => 1, 'name' => 'Syrup - Vanilla', 'unit_id' => 3, 'unit_price' => 420.00],
            ['category_id' => 1, 'name' => 'Tea Bags - Black', 'unit_id' => 4, 'unit_price' => 280.00], // Price per Box of bags
            ['category_id' => 1, 'name' => 'Sugar Packets', 'unit_id' => 4, 'unit_price' => 950.00], // Price per large Box of packets
            ['category_id' => 1, 'name' => 'Pastries/Baked Goods', 'unit_id' => 5, 'unit_price' => 45.00],

            // --- Category ID 2: Disposables ---
            ['category_id' => 2, 'name' => 'Hot Cups - 12oz', 'unit_id' => 6, 'unit_price' => 250.00], // Price per Sleeve
            ['category_id' => 2, 'name' => 'Lids - 12oz', 'unit_id' => 6, 'unit_price' => 180.00],
            ['category_id' => 2, 'name' => 'Cup Sleeves', 'unit_id' => 7, 'unit_price' => 350.00],
            ['category_id' => 2, 'name' => 'Cold Cups - 16oz', 'unit_id' => 6, 'unit_price' => 320.00],
            ['category_id' => 2, 'name' => 'Straws', 'unit_id' => 4, 'unit_price' => 450.00],
            ['category_id' => 2, 'name' => 'Napkins', 'unit_id' => 8, 'unit_price' => 1200.00],
            ['category_id' => 2, 'name' => 'Stirrers', 'unit_id' => 4, 'unit_price' => 210.00],
            ['category_id' => 2, 'name' => 'Takeout Drink Carriers', 'unit_id' => 7, 'unit_price' => 550.00],

            // --- Category ID 3: Barista Tools & Accessories ---
            ['category_id' => 3, 'name' => 'Frothing Pitchers', 'unit_id' => 5, 'unit_price' => 450.00], // Price per Piece
            ['category_id' => 3, 'name' => 'Espresso Tamper', 'unit_id' => 5, 'unit_price' => 1200.00],
            ['category_id' => 3, 'name' => 'Knock Box', 'unit_id' => 5, 'unit_price' => 980.00],
            ['category_id' => 3, 'name' => 'Digital Scale', 'unit_id' => 5, 'unit_price' => 850.00],
            ['category_id' => 3, 'name' => 'Shot Glasses/Jiggers', 'unit_id' => 5, 'unit_price' => 150.00],
            ['category_id' => 3, 'name' => 'Syrup Pumps', 'unit_id' => 5, 'unit_price' => 80.00],

            // --- Category ID 4: Equipment ---
            ['category_id' => 4, 'name' => 'Commercial Espresso Machine', 'unit_id' => 12, 'unit_price' => 150000.00], // Price per Unit
            ['category_id' => 4, 'name' => 'Espresso Grinder', 'unit_id' => 12, 'unit_price' => 35000.00],
            ['category_id' => 4, 'name' => 'Drip Coffee Brewer', 'unit_id' => 12, 'unit_price' => 15000.00],
            ['category_id' => 4, 'name' => 'Water Filtration System', 'unit_id' => 12, 'unit_price' => 8000.00],
            ['category_id' => 4, 'name' => 'Commercial Refrigerator', 'unit_id' => 12, 'unit_price' => 45000.00],
            ['category_id' => 4, 'name' => 'POS Terminal', 'unit_id' => 12, 'unit_price' => 25000.00],

            // --- Category ID 5: Cleaning & Sanitation ---
            ['category_id' => 5, 'name' => 'Espresso Machine Backflush Detergent', 'unit_id' => 9, 'unit_price' => 600.00], // Price per Jar
            ['category_id' => 5, 'name' => 'Grinder Cleaning Tablets', 'unit_id' => 9, 'unit_price' => 750.00],
            ['category_id' => 5, 'name' => 'Dish Soap - Commercial Grade', 'unit_id' => 2, 'unit_price' => 120.00],
            ['category_id' => 5, 'name' => 'Sanitizing Wipes', 'unit_id' => 10, 'unit_price' => 300.00],
            ['category_id' => 5, 'name' => 'Floor Mop and Bucket', 'unit_id' => 11, 'unit_price' => 850.00],
            ['category_id' => 5, 'name' => 'Hand Soap', 'unit_id' => 3, 'unit_price' => 90.00],

            // --- Category ID 6: Furniture & Decor ---
            ['category_id' => 6, 'name' => 'Customer Tables', 'unit_id' => 12, 'unit_price' => 3500.00], // Price per Unit
            ['category_id' => 6, 'name' => 'Dining Chairs', 'unit_id' => 5, 'unit_price' => 950.00],
            ['category_id' => 6, 'name' => 'Wall Shelving Unit', 'unit_id' => 12, 'unit_price' => 2800.00],
            ['category_id' => 6, 'name' => 'Ceramic Coffee Mugs', 'unit_id' => 5, 'unit_price' => 80.00],
            ['category_id' => 6, 'name' => 'Glassware for Iced Drinks', 'unit_id' => 5, 'unit_price' => 75.00],
            ['category_id' => 6, 'name' => 'Menu Display Board', 'unit_id' => 12, 'unit_price' => 1500.00],
        ];
        foreach ($items as $item) {
            Item::create($item);
        }
    }
}

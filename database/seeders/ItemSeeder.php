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
        //
        $items = [
            // --- Category ID 1: Raw Materials ---
            ['category_id' => 1, 'name' => 'Coffee Beans - Espresso Blend'],
            ['category_id' => 1, 'name' => 'Coffee Beans - Drip Roast'],
            ['category_id' => 1, 'name' => 'Milk - Whole'],
            ['category_id' => 1, 'name' => 'Milk - Oat Alternative'],
            ['category_id' => 1, 'name' => 'Syrup - Vanilla'],
            ['category_id' => 1, 'name' => 'Tea Bags - Black'],
            ['category_id' => 1, 'name' => 'Sugar Packets'],
            ['category_id' => 1, 'name' => 'Pastries/Baked Goods'],

            // --- Category ID 2: Disposables ---
            ['category_id' => 2, 'name' => 'Hot Cups - 12oz'],
            ['category_id' => 2, 'name' => 'Lids - 12oz'],
            ['category_id' => 2, 'name' => 'Cup Sleeves'],
            ['category_id' => 2, 'name' => 'Cold Cups - 16oz'],
            ['category_id' => 2, 'name' => 'Straws'],
            ['category_id' => 2, 'name' => 'Napkins'],
            ['category_id' => 2, 'name' => 'Stirrers'],
            ['category_id' => 2, 'name' => 'Takeout Drink Carriers'],

            // --- Category ID 3: Barista Tools & Accessories ---
            ['category_id' => 3, 'name' => 'Frothing Pitchers'],
            ['category_id' => 3, 'name' => 'Espresso Tamper'],
            ['category_id' => 3, 'name' => 'Knock Box'],
            ['category_id' => 3, 'name' => 'Digital Scale'],
            ['category_id' => 3, 'name' => 'Shot Glasses/Jiggers'],
            ['category_id' => 3, 'name' => 'Syrup Pumps'],

            // --- Category ID 4: Equipment ---
            ['category_id' => 4, 'name' => 'Commercial Espresso Machine'],
            ['category_id' => 4, 'name' => 'Espresso Grinder'],
            ['category_id' => 4, 'name' => 'Drip Coffee Brewer'],
            ['category_id' => 4, 'name' => 'Water Filtration System'],
            ['category_id' => 4, 'name' => 'Commercial Refrigerator'],
            ['category_id' => 4, 'name' => 'POS Terminal'],

            // --- Category ID 5: Cleaning & Sanitation ---
            ['category_id' => 5, 'name' => 'Espresso Machine Backflush Detergent'],
            ['category_id' => 5, 'name' => 'Grinder Cleaning Tablets'],
            ['category_id' => 5, 'name' => 'Dish Soap - Commercial Grade'],
            ['category_id' => 5, 'name' => 'Sanitizing Wipes'],
            ['category_id' => 5, 'name' => 'Floor Mop and Bucket'],
            ['category_id' => 5, 'name' => 'Hand Soap'],

            // --- Category ID 6: Furniture & Decor ---
            ['category_id' => 6, 'name' => 'Customer Tables'],
            ['category_id' => 6, 'name' => 'Dining Chairs'],
            ['category_id' => 6, 'name' => 'Wall Shelving Unit'],
            ['category_id' => 6, 'name' => 'Ceramic Coffee Mugs'],
            ['category_id' => 6, 'name' => 'Glassware for Iced Drinks'],
            ['category_id' => 6, 'name' => 'Menu Display Board'],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}

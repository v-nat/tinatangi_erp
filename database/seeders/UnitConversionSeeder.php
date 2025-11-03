<?php

namespace Database\Seeders;

use App\Models\UnitConversion;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitConversionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conversions = [
            // [from_id, to_id (base unit), factor]

            // --- Weight Conversions (to Gram, ID: 13) ---
            [1, 13, 1000], // 1 Kilogram (1) = 1000 Grams (13)

            // --- Volume Conversions (to Milliliter, ID: 14) ---
            [2, 14, 1000], // 1 Liter (2) = 1000 Milliliters (14)
            [3, 14, 750], // 1 Bottle (3) = 750 Milliliters (14)
            [9, 14, 240],      // 1 Jar (9) = 240 Milliliters (14)


            // --- Count Conversions (to Piece(s), ID: 5) ---
            // NOTE: Adjust these factors to match your actual supplies.
            [4, 5, 12],     // 1 Box (4) = 12 Pieces
            [6, 5, 50],     // 1 Sleeve (6) = 50 Pieces (e.g., a sleeve of cups)
            [7, 5, 10],     // 1 Bundle (7) = 10 Pieces
            [8, 5, 24],     // 1 Case (8) = 24 Pieces
            [10, 5, 6],     // 1 Tub (10) = 6 Pieces
            [11, 5, 4],     // 1 Set (11) = 4 Pieces
            [12, 5, 1],     // 1 Unit (12) = 1 Piece
            [15, 5, 100],   // 1 Bag (15) = 100 Pieces (e.g., a bag of sugar packets)
            [16, 5, 1],     // 1 Sachet (16) = 1 Piece
            [17, 5, 1],     // 1 Roll (17) = 1 Piece
            [18, 5, 6],     // 1 Pack (18) = 6 Pieces
        ];

        $dataToSeed = [];
        $id = 0;

        foreach ($conversions as $rule) {
            $dataToSeed[] = [
                // 'id' => $id++,
                'from_unit_id' => $rule[0],
                'to_unit_id'   => $rule[1],
                'factor'       => $rule[2],
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        // Use upsert to efficiently create or update all conversion rules at once.
        UnitConversion::upsert(
            $dataToSeed,
            ['from_unit_id', 'to_unit_id'],
            ['factor', 'updated_at']
        );
    }
}

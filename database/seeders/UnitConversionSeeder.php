<?php

namespace Database\Seeders;

use App\Models\ItemUnit;
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
        $gram = ItemUnit::where('name', 'Gram')->first();
        $milliliter = ItemUnit::where('name', 'Milliliter')->first();
        $piece = ItemUnit::where('name', 'Piece(s)')->first();

        // --- Weight Conversions (to Gram) ---
        if ($gram) {
            UnitConversion::create([
                'from_unit_id' => ItemUnit::where('name', 'Kilogram')->first()->id,
                'to_unit_id' => $gram->id,
                'factor' => 1000, // 1 Kilogram = 1000 Grams
            ]);
        }

        // --- Volume Conversions (to Milliliter) ---
        if ($milliliter) {
            UnitConversion::create([
                'from_unit_id' => ItemUnit::where('name', 'Liter')->first()->id,
                'to_unit_id' => $milliliter->id,
                'factor' => 1000, // 1 Liter = 1000 Milliliters
            ]);
        }

        if ($piece) {
            UnitConversion::create([
                'from_unit_id' => ItemUnit::where('name', 'Box')->first()->id,
                'to_unit_id' => $piece->id,
                'factor' => 12, // Example: 1 Box = 12 Pieces
            ]);
            UnitConversion::create([
                'from_unit_id' => ItemUnit::where('name', 'Case')->first()->id,
                'to_unit_id' => $piece->id,
                'factor' => 24, // Example: 1 Case = 24 Pieces
            ]);
            UnitConversion::create([
                'from_unit_id' => ItemUnit::where('name', 'Pack')->first()->id,
                'to_unit_id' => $piece->id,
                'factor' => 6, // Example: 1 Pack = 6 Pieces
            ]);
        }
    }
}

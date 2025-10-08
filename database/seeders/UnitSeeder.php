<?php

namespace Database\Seeders;

use App\Models\ItemUnit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Full Name => Abbreviation
            'Kilogram'      => 'KG',
            'Liter'         => 'LITER',
            'Bottle'        => 'BOTTLE',
            'Box'           => 'BOX',
            'Piece(s)'      => 'PCS',
            'Sleeve'        => 'SLEEVE',
            'Bundle'        => 'BUNDLE',
            'Case'          => 'CASE',
            'Jar'           => 'JAR',
            'Tub'           => 'TUB',
            'Set'           => 'SET',
            'Unit'          => 'UNIT',
            // Adding more common units from previous request for completeness
            'Gram'          => 'G',
            'Milliliter'    => 'ML',
            'Bag'           => 'BAG',
            'Sachet'        => 'SACHET',
            'Roll'          => 'ROLL',
            'Pack'          => 'PACK',
        ];

        $dataToSeed = [];
        $idCounter = 1; // Initialize ID counter to assign explicit IDs

        foreach ($units as $fullName => $abbreviation) {
            $dataToSeed[] = [
                'id' => $idCounter++,
                'name' => $fullName,
                'abbreviation' => $abbreviation,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ItemUnit::upsert($dataToSeed, ['id'], ['name', 'abbreviation', 'updated_at']);
    }
}

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
        // ⭐ Restructured data for easier type and base unit assignment
        $unitGroups = [
            'weight' => [
                'Gram' => ['abbreviation' => 'G', 'is_base' => true],
                'Kilogram' => ['abbreviation' => 'KG', 'is_base' => false],
            ],
            'volume' => [
                'Milliliter' => ['abbreviation' => 'ML', 'is_base' => true],
                'Liter' => ['abbreviation' => 'LITER', 'is_base' => false],
            ],
            'count' => [
                'Piece(s)' => ['abbreviation' => 'PCS', 'is_base' => true],
                'Bottle' => ['abbreviation' => 'BOTTLE', 'is_base' => false],
                'Box' => ['abbreviation' => 'BOX', 'is_base' => false],
                'Sleeve' => ['abbreviation' => 'SLEEVE', 'is_base' => false],
                'Bundle' => ['abbreviation' => 'BUNDLE', 'is_base' => false],
                'Case' => ['abbreviation' => 'CASE', 'is_base' => false],
                'Jar' => ['abbreviation' => 'JAR', 'is_base' => false],
                'Tub' => ['abbreviation' => 'TUB', 'is_base' => false],
                'Set' => ['abbreviation' => 'SET', 'is_base' => false],
                'Unit' => ['abbreviation' => 'UNIT', 'is_base' => false],
                'Bag' => ['abbreviation' => 'BAG', 'is_base' => false],
                'Sachet' => ['abbreviation' => 'SACHET', 'is_base' => false],
                'Roll' => ['abbreviation' => 'ROLL', 'is_base' => false],
                'Pack' => ['abbreviation' => 'PACK', 'is_base' => false],
            ],
        ];

        $dataToSeed = [];

        // Loop through the groups to build the data array
        foreach ($unitGroups as $type => $units) {
            foreach ($units as $name => $details) {
                $dataToSeed[] = [
                    'name' => $name,
                    'abbreviation' => $details['abbreviation'],
                    'type' => $type, // Set the measurement type
                    'is_base_unit' => $details['is_base'], // Set if it's a base unit
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Use upsert to efficiently create or update records based on the name
        ItemUnit::upsert($dataToSeed, ['name'], ['abbreviation', 'type', 'is_base_unit', 'updated_at']);
    }
}

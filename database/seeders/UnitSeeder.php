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
            'Kilogram'    => 'KG',
            'Liter'       => 'LITER',
            'Bottle'      => 'BOTTLE',
            'Box'         => 'BOX',
            'Piece(s)'    => 'PCS',
            'Sleeve'      => 'SLEEVE',
            'Bundle'      => 'BUNDLE',
            'Case'        => 'CASE',
            'Jar'         => 'JAR',
            'Tub'         => 'TUB',
            'Set'         => 'SET',
            'Unit'        => 'UNIT',
        ];

        $dataToSeed = [];
        $idCounter = 1; // Initialize ID counter to assign explicit IDs

        foreach ($units as $fullName => $abbreviation) {
            $dataToSeed[] = [
                'id' => $idCounter++,           // Explicitly define the ID
                'name' => $fullName,            // Complete unit name
                'abbreviation' => $abbreviation, // Abbreviated unit name
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Use upsert to insert or update existing records based on the unique 'id' column,
        // ensuring the sequential IDs are preserved if the seeder is re-run.
        ItemUnit::upsert($dataToSeed, ['id'], ['name', 'abbreviation', 'updated_at']);
    }
}

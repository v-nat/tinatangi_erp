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
            // id => [name, abbreviation, type, is_base_unit]
            1 => ['Kilogram', 'KG', 'weight', false],
            2 => ['Liter', 'LITER', 'volume', false],
            3 => ['Bottle', 'BOTTLE', 'volume', false],
            4 => ['Box', 'BOX', 'count', false],
            5 => ['Piece(s)', 'PCS', 'count', true], // Base unit for count
            6 => ['Sleeve', 'SLEEVE', 'count', false],
            7 => ['Bundle', 'BUNDLE', 'count', false],
            8 => ['Case', 'CASE', 'count', false],
            9 => ['Jar', 'JAR', 'count', false],
            10 => ['Tub', 'TUB', 'count', false],
            11 => ['Set', 'SET', 'count', false],
            12 => ['Unit', 'UNIT', 'count', false],
            13 => ['Gram', 'G', 'weight', true], // Base unit for weight
            14 => ['Milliliter', 'ML', 'volume', true], // Base unit for volume
            15 => ['Bag', 'BAG', 'count', false],
            16 => ['Sachet', 'SACHET', 'count', false],
            17 => ['Roll', 'ROLL', 'count', false],
            18 => ['Pack', 'PACK', 'count', false],
        ];

        $dataToSeed = [];

        foreach ($units as $id => $details) {
            $dataToSeed[] = [
                'id' => $id,
                'name' => $details[0],
                'abbreviation' => $details[1],
                'type' => $details[2],
                'is_base_unit' => $details[3],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ItemUnit::upsert($dataToSeed, ['id'], ['name', 'abbreviation', 'type', 'is_base_unit', 'updated_at']);
    }
}

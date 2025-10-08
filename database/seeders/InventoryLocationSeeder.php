<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Bakery Department',
            'Coffee Department',
            'Kitchen Department',
            'Equipment Room',
            'Storage Room',
            'Cleaning Supplies Room',
            'Staff Area',
        ];

        foreach ($locations as $locationName) {
            DB::table('inventory_locations')->insert([
                'name' => $locationName,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $suppliers = [
            [
                'id' => 23,
                'user_id' => 23,
                'supplier_name' => 'Premium Coffee Roasters Inc.',
                'email' => 'sales@premiumroasters.com',
                'phone_number' => '09171234567',
                'status' => 1,
                'created_at' => '2025-09-08 12:00:00',
                'updated_at' => '2025-09-08 12:00:00',
            ],
            [
                'id' => 24,
                'user_id' => 24,
                'supplier_name' => 'Cup & Lid Supplies Co.',
                'email' => 'orders@cuplids.net',
                'phone_number' => '09199876543',
                'status' => 1,
                'created_at' => '2025-09-08 12:00:00',
                'updated_at' => '2025-09-08 12:00:00',
            ],
            [
                'id' => 25,
                'user_id' => 25,
                'supplier_name' => 'Equipment & Tools Trading',
                'email' => 'support@toolstrade.com',
                'phone_number' => '09205551234',
                'status' => 1,
                'created_at' => '2025-09-08 12:00:00',
                'updated_at' => '2025-09-08 12:00:00',
            ],
        ];

        DB::table('suppliers')->insert($suppliers);
    }
}

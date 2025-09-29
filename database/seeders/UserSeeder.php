<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = [
            [
                'id' => 1,
                'first_name' => 'Taylor',
                'last_name' => 'Swift',
                'email' => 'ts13@mail.com',
                'password' => bcrypt('password'),
                'phone_number' => '09123456789',
                'user_type' => 'employee',
                'created_at' => now(), // Add timestamps
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'first_name' => 'Nathaniel',
                'last_name' => 'Vasquez',
                'email' => 'nat@mail.com',
                'password' => bcrypt('password'),
                'phone_number' => '09123456789',
                'user_type' => 'employee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 23,
                'first_name' => 'Premium Coffee Roasters Inc.',
                'last_name' => 'Supplier',
                'email' => 'sales@premiumroasters.com',
                'password' => bcrypt('password'),
                'phone_number' => '09171234567',
                'user_type' => 'supplier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 24,
                'first_name' => 'Cup & Lid Supplies Co.',
                'last_name' => 'Supplier',
                'email' => 'orders@cuplids.net',
                'password' => bcrypt('password'),
                'phone_number' => '09199876543',
                'user_type' => 'supplier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 25,
                'first_name' => 'Equipment & Tools Trading',
                'last_name' => 'Supplier',
                'email' => 'support@toolstrade.com',
                'password' => bcrypt('password'),
                'phone_number' => '09205551234',
                'user_type' => 'supplier',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
        ];

        DB::table('users')->insert($users);
    }
}

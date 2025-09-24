<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('employees')->insert([
            [
                'id'            => 1,
                'user_id'       => 1, // must exist in users table
                'address'       => 'Tennessee',
                'postal_code'   => 19611,
                'gender'        => 'Female',
                'birth_date'    => '1989-12-13',
                'age'           => Carbon::parse('1989-12-13')->age,
                'phone_number'  => '09171234567',
                'citizenship'   => 'American',

                'department'    => 1, // must exist in departments table
                'supervisor_id' => 1, // or valid employee ID
                'level'         => 'ceo',
                'position_id'   => 1, // must exist in positions table

                'sss'           => 600.00,
                'pagibig'       => 100.00,
                'philhealth'    => 450.00,
                'base_salary'   => 20800.00,

                'status'        => 1, // must exist in status table
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
        DB::table('employees')->insert([
            [
                'id'            => 2,
                'user_id'       => 2, // must exist in users table
                'address'       => 'Cavite',
                'postal_code'   => 4107,
                'gender'        => 'Male',
                'birth_date'    => '2003-08-21',
                'age'           => Carbon::parse('2003-08-21')->age,
                'phone_number'  => '09070959723',
                'citizenship'   => 'Filipino',

                'department'    => 2, // must exist in departments table
                'supervisor_id' => 1, // or valid employee ID
                'level'         => 'manager',
                'position_id'   => 2, // must exist in positions table

                'sss'           => 600.00,
                'pagibig'       => 100.00,
                'philhealth'    => 450.00,
                'base_salary'   => 20800.00,

                'status'        => 1, // must exist in status table
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}

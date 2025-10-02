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
                'user_id'       => 1,
                'address'       => 'Tennessee',
                'postal_code'   => 19611,
                'gender'        => 'Female',
                'birth_date'    => '1989-12-13',
                'age'           => Carbon::parse('1989-12-13')->age,
                'phone_number'  => '09171234567',
                'citizenship'   => 'American',

                'department'    => 1,
                'supervisor_id' => 1,
                'level'         => 'ceo',
                'position_id'   => 1,

                'sss'           => 600.00,
                'pagibig'       => 100.00,
                'philhealth'    => 450.00,
                'base_salary'   => 20800.00,

                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
        DB::table('employees')->insert([
            [
                'id'            => 2,
                'user_id'       => 2,
                'address'       => 'Cavite',
                'postal_code'   => 4107,
                'gender'        => 'Male',
                'birth_date'    => '2003-08-21',
                'age'           => Carbon::parse('2003-08-21')->age,
                'phone_number'  => '09070959723',
                'citizenship'   => 'Filipino',

                'department'    => 2,
                'supervisor_id' => 1,
                'level'         => 'manager',
                'position_id'   => 2,

                'sss'           => 600.00,
                'pagibig'       => 100.00,
                'philhealth'    => 450.00,
                'base_salary'   => 20800.00,

                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
        DB::table('employees')->insert([
            [
                'id'            => 3,
                'user_id'       => 3,
                'address'       => 'Cavite',
                'postal_code'   => 4107,
                'gender'        => 'Male',
                'birth_date'    => '2003-08-21',
                'age'           => Carbon::parse('2003-08-21')->age,
                'phone_number'  => '09070959723',
                'citizenship'   => 'Filipino',

                'department'    => 3,
                'supervisor_id' => 1,
                'level'         => 'manager',
                'position_id'   => 4,

                'sss'           => 600.00,
                'pagibig'       => 100.00,
                'philhealth'    => 450.00,
                'base_salary'   => 20800.00,

                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
        DB::table('employees')->insert([
            [
                'id'            => 4,
                'user_id'       => 4,
                'address'       => 'Cavite',
                'postal_code'   => 4107,
                'gender'        => 'Male',
                'birth_date'    => '2003-08-21',
                'age'           => Carbon::parse('2003-08-21')->age,
                'phone_number'  => '09070959723',
                'citizenship'   => 'Filipino',

                'department'    => 4,
                'supervisor_id' => 1,
                'level'         => 'manager',
                'position_id'   => 5,

                'sss'           => 600.00,
                'pagibig'       => 100.00,
                'philhealth'    => 450.00,
                'base_salary'   => 20800.00,

                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}

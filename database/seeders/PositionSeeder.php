<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $positions = [
            ['name' => 'CEO', 'department_id' => 1],
            ['name' => 'HR Manager',  'level' => 'manager', 'department_id' => 2],
            ['name' => 'HR Assistant',  'level' => 'staff', 'department_id' => 2],
            ['name' => 'Finance Manager',  'level' => 'manager', 'department_id' => 3],
            ['name' => 'Procurement Manager',  'level' => 'manager', 'department_id' => 4],
            ['name' => 'CS Manager',  'level' => 'manager', 'department_id' => 5],
            ['name' => 'CS Staff',  'level' => 'staff', 'department_id' => 5],
            ['name' => 'Kitchen Supervisor',  'level' => 'supervisor', 'department_id' => 8],
            ['name' => 'Cook',  'level' => 'staff', 'department_id' => 8],
            ['name' => 'Kitchen Assistant',  'level' => 'staff', 'department_id' => 8],
            ['name' => 'Barista Supervisor',  'level' => 'supervisor', 'department_id' => 7],
            ['name' => 'Senior Barista',  'level' => 'staff', 'department_id' => 7],
            ['name' => 'Junior Barista',  'level' => 'staff', 'department_id' => 7],
            ['name' => 'Cashier',  'level' => 'staff', 'department_id' => 6],
            ['name' => 'Waitstaff',  'level' => 'staff', 'department_id' => 6],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}

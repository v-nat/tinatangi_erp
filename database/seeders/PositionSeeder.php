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
            [ 'id' =>  1, 'name' => 'CEO', 'department_id' => 1],
            [ 'id' =>  2, 'name' => 'HR Manager',  'level' => 'manager', 'department_id' => 2],
            [ 'id' =>  3, 'name' => 'HR Assistant',  'level' => 'staff', 'department_id' => 2],
            [ 'id' =>  4, 'name' => 'Finance Manager',  'level' => 'manager', 'department_id' => 3],
            [ 'id' =>  5, 'name' => 'Procurement Manager',  'level' => 'manager', 'department_id' => 4],
            [ 'id' =>  6, 'name' => 'Inventory Manager',  'level' => 'manager', 'department_id' => 5],
            [ 'id' =>  7, 'name' => 'Inventory Clerk',  'level' => 'staff', 'department_id' => 5],
            [ 'id' =>  8, 'name' => 'CS Manager',  'level' => 'manager', 'department_id' => 6],
            [ 'id' =>  9, 'name' => 'CS Staff',  'level' => 'staff', 'department_id' => 6],
            [ 'id' =>  10, 'name' => 'Kitchen Supervisor',  'level' => 'supervisor', 'department_id' => 9],
            [ 'id' =>  11, 'name' => 'Cook',  'level' => 'staff', 'department_id' => 9],
            [ 'id' =>  12, 'name' => 'Kitchen Assistant',  'level' => 'staff', 'department_id' => 9],
            [ 'id' =>  13, 'name' => 'Barista Supervisor',  'level' => 'supervisor', 'department_id' => 8],
            [ 'id' =>  14, 'name' => 'Senior Barista',  'level' => 'staff', 'department_id' => 8],
            [ 'id' =>  15, 'name' => 'Junior Barista',  'level' => 'staff', 'department_id' => 8],
            [ 'id' =>  16, 'name' => 'Cashier',  'level' => 'staff', 'department_id' => 7],
            [ 'id' =>  17, 'name' => 'Waitstaff',  'level' => 'staff', 'department_id' => 7],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}

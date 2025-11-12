<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import the DB facade
// App\Models\Department is no longer needed if using DB::table()
// use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->truncate();

        $departments = [
            ['id' => 0, 'name' => 'Administration'],
            ['id' => 1, 'name' => 'Executives'],
            ['id' => 2, 'name' => 'Human Resources'],
            ['id' => 3, 'name' => 'Finance and Accounting'],
            ['id' => 4, 'name' => 'Procurement'],
            ['id' => 5, 'name' => 'Inventory'],
            ['id' => 6, 'name' => 'Customer Service'],
            ['id' => 7, 'name' => 'Service Operations'],
            ['id' => 8, 'name' => 'Barista Department'],
            ['id' => 9, 'name' => 'Kitchen Department'],
        ];

        DB::table('departments')->insert($departments);
    }
}

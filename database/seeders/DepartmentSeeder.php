<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $departments = [
            'Human Resources Management',
            'Finance Risk Management',
            'Supply Chain Management',
            'Manufacturing',
            'Customer Relationship Management',
        ];

        foreach ($departments as $name) {
            Department::create(['name' => $name]);
        }
    }
}

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
            'Administrator',
            'Human Resources Management',
            'Finance Risk Management',
            'Procurement',
            'Customer Service',
            'Service Operations',
            'Barista Department',
            'Kitchen Department',
        ];

        foreach ($departments as $name) {
            Department::create(['name' => $name]);
        }
    }
}

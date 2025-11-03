<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeSalarySettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SalarySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('employee_salary_settings')->truncate();

        $salaries = [
            ['id' => 2, 'salary' => 20800.00],
            ['id' => 3, 'salary' => 16640.00],
            ['id' => 4, 'salary' => 20800.00],
            ['id' => 5, 'salary' => 20800.00],
            ['id' => 6, 'salary' => 20800.00],
            ['id' => 7, 'salary' => 13520.00],
            ['id' => 8, 'salary' => 20800.00],
            ['id' => 9, 'salary' => 16640.00],
            ['id' => 10, 'salary' => 20800.00],
            ['id' => 11, 'salary' => 15600.00],
            ['id' => 12, 'salary' => 13520.00],
            ['id' => 13, 'salary' => 20800.00],
            ['id' => 14, 'salary' => 16640.00],
            ['id' => 15, 'salary' => 13520.00],
            ['id' => 16, 'salary' => 14560.00],
            ['id' => 17, 'salary' => 13520.00],
        ];

        foreach ($salaries as $data) {
            $baseSalary = $data['salary'];
            $ratePerDay = $baseSalary / 26;
            $ratePerHour = $ratePerDay / 8;

            EmployeeSalarySettings::create([
                'position_id'   => $data['id'],
                'base_salary'   => $baseSalary,
                'rate_per_hour' => $ratePerHour,
                'rate_per_day'  => $ratePerDay,
                'status'        => 1,
            ]);
        }
    }
}

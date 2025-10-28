<?php

namespace Database\Seeders;

use App\Models\PayrollSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PayrollSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payroll_settings')->truncate();

        PayrollSettings::create([
            'name'       => 'Default Contributions',
            'sss'        => 600.00,
            'philhealth' => 450.00,
            'pagibig'    => 100.00,
            'status'     => 1,
        ]);
    }
}

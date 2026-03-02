<?php

namespace Database\Seeders;

use App\Models\ContributionRate;
use Illuminate\Database\Seeder;

class ContributionRateSeeder extends Seeder
{
    public function run(): void
    {
        ContributionRate::create([
            'sss_employee_rate'        => 0.0450,
            'sss_employer_rate'        => 0.0950,
            'philhealth_employee_rate' => 0.0250,
            'philhealth_employer_rate' => 0.0250,
            'pagibig_employee_rate'    => 0.0200,
            'pagibig_employer_rate'    => 0.0200,
            'effective_date'           => '2026-01-01',
            'is_active'                => 1,
            'created_by'               => null,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Attendance extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $employeeId = 202532606;

        // Start date: September 1, 2025
        $startDate = Carbon::create(2025, 8, 1);

        $records = [];

        // Loop 13 days from the start date
        for ($i = 0; $i < 13; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();

            $records[] = [
                'employee_id'        => $employeeId,
                'date'               => $date,
                'time_in'            => '09:00:00',
                'time_out'           => '17:00:00',
                'hours_worked'       => 480.00,
                'tardiness'          => 0,
                'is_leave'           => 0,
                'tardiness_minutes'  => 0,
                'leave_id'           => null,
                'overtime_minutes'   => 0,
                'overtime_id'        => null,
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ];
        }

        DB::table('attendances')->insert($records);
    }
}

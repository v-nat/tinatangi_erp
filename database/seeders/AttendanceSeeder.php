<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Seeds realistic attendance data for the current week so the
     * HR dashboard Weekly Attendance chart looks populated.
     *
     * Status codes:  6 = On Time | 9 = Late | 8 = On Leave
     */
    public function run(): void
    {
        // Grab all active non-CEO employees
        $employees = DB::table('employees')
            ->join('positions', 'employees.position_id', '=', 'positions.id')
            ->whereNull('employees.deleted_at')
            ->where('positions.level', '!=', 'ceo')
            ->pluck('employees.id')
            ->toArray();

        if (empty($employees)) {
            $this->command->warn('No employees found. Run employee seeders first.');
            return;
        }

        $weekStart = Carbon::now()->startOfWeek(); // Monday
        $today     = Carbon::today();

        $records = [];

        foreach ($employees as $empId) {
            // Seed every working day from Monday up to (and including) today
            for ($d = $weekStart->copy(); $d->lte($today); $d->addDay()) {
                // Skip weekends (Saturday = 6, Sunday = 0)
                if ($d->isSaturday() || $d->isSunday()) {
                    continue;
                }

                // Skip if a record already exists for this employee+date
                $exists = DB::table('attendances')
                    ->where('employee_id', $empId)
                    ->where('date', $d->toDateString())
                    ->whereNull('deleted_at')
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Distribute statuses: ~65% On Time, ~25% Late, ~10% On Leave
                $roll = rand(1, 100);

                if ($roll <= 10) {
                    // On Leave
                    $records[] = [
                        'employee_id'       => $empId,
                        'date'              => $d->toDateString(),
                        'time_in'           => null,
                        'time_out'          => null,
                        'hours_worked'      => 0,
                        'tardiness'         => 0,
                        'tardiness_minutes' => 0,
                        'overtime_minutes'  => 0,
                        'is_leave'          => true,
                        'is_paid_leave'     => true,
                        'leave_id'          => null,
                        'overtime_id'       => null,
                        'status'            => 8, // On Leave
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                } elseif ($roll <= 35) {
                    // Late — arrives 10–45 min after shift start (09:00)
                    $lateMinutes = rand(10, 45);
                    $timeIn      = Carbon::createFromTime(9, $lateMinutes, 0);
                    $timeOut     = Carbon::createFromTime(18, 0, 0);
                    $hoursWorked = round($timeOut->diffInMinutes($timeIn) / 60, 2);

                    $records[] = [
                        'employee_id'       => $empId,
                        'date'              => $d->toDateString(),
                        'time_in'           => $timeIn->format('H:i:s'),
                        'time_out'          => $timeOut->format('H:i:s'),
                        'hours_worked'      => $hoursWorked,
                        'tardiness'         => 1,
                        'tardiness_minutes' => $lateMinutes,
                        'overtime_minutes'  => 0,
                        'is_leave'          => false,
                        'is_paid_leave'     => false,
                        'leave_id'          => null,
                        'overtime_id'       => null,
                        'status'            => 9, // Late
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                } else {
                    // On Time — shift 09:00–18:00, some with minor overtime
                    $overtimeMinutes = (rand(1, 10) <= 3) ? rand(15, 90) : 0;
                    $timeIn          = Carbon::createFromTime(8, rand(45, 59), 0);
                    $timeOut         = Carbon::createFromTime(18, 0, 0)->addMinutes($overtimeMinutes);
                    $hoursWorked     = round($timeOut->diffInMinutes($timeIn) / 60, 2);

                    $records[] = [
                        'employee_id'       => $empId,
                        'date'              => $d->toDateString(),
                        'time_in'           => $timeIn->format('H:i:s'),
                        'time_out'          => $timeOut->format('H:i:s'),
                        'hours_worked'      => $hoursWorked,
                        'tardiness'         => 0,
                        'tardiness_minutes' => 0,
                        'overtime_minutes'  => $overtimeMinutes,
                        'is_leave'          => false,
                        'is_paid_leave'     => false,
                        'leave_id'          => null,
                        'overtime_id'       => null,
                        'status'            => 6, // On Time
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }
            }
        }

        if (empty($records)) {
            $this->command->info('No new attendance records to insert (all already exist).');
            return;
        }

        // Insert in chunks to avoid hitting query size limits
        foreach (array_chunk($records, 100) as $chunk) {
            DB::table('attendances')->insert($chunk);
        }

        $this->command->info('Inserted ' . count($records) . ' attendance records for ' . count($employees) . ' employees.');
    }
}

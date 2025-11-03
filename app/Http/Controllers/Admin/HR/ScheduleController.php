<?php

namespace App\Http\Controllers\Admin\HR;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function getSchedules(Request $request)
    {
        $schedules = Schedule::with('employee.user')->get();

        $events = [];

        foreach ($schedules as $schedule) {
            $events[] = [
                'id'           => $schedule->id,
                'title'        => $schedule->title ?: ($schedule->employee->user->name ?? 'Scheduled'),
                'daysOfWeek'   => $schedule->days_of_week,
                'startTime'    => $schedule->time_in,
                'endTime'      => $schedule->time_out,
                'color'        => $schedule->color,
                'extendedProps' => [
                    'employee_id' => $schedule->employee_id,
                    'description' => $schedule->description,
                ],
                // You might need 'startRecur' and 'endRecur' if shifts have specific date ranges
                // 'startRecur' => '2025-11-01',
                // 'endRecur' => '2026-05-01',
            ];
        }

        return response()->json($events);
    }
}

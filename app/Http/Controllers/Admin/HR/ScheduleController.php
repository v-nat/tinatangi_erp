<?php

namespace App\Http\Controllers\Admin\HR;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    public function viewEmployeeSchedule(string $id)
    {
        if ((string) Auth::id() !== (string) $id && Auth::user()?->user_type === 'employee') {
            abort(403);
        }

        return view('pages.admin.human_resources.employee-schedule', compact('id'));
    }

    public function getEmployeeScheduleEvents(string $id)
    {
        if ((string) Auth::id() !== (string) $id && Auth::user()?->user_type === 'employee') {
            abort(403);
        }

        $schedules = Schedule::where('employee_id', $id)->get();

        $events = $schedules->map(function (Schedule $schedule) {
            $daysOfWeek = collect($schedule->days_of_week ?? [])
                ->map(fn($day) => (int) $day)
                ->filter(fn($day) => $day >= 0 && $day <= 6)
                ->values()
                ->toArray();

            $startTime = optional($schedule->time_in)->format('H:i');
            $endTime = optional($schedule->time_out)->format('H:i');

            $timeLabel = trim(sprintf('%s%s%s', $startTime, ($startTime && $endTime) ? ' - ' : '', $endTime));

            return [
                'id' => 'employee-schedule-' . $schedule->id,
                'title' => $schedule->title ?: 'Scheduled Shift',
                'daysOfWeek' => $daysOfWeek,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'display' => 'block',
                'color' => $schedule->color ?: '#3788D8',
                'textColor' => '#ffffff',
                'allDay' => false,
                'extendedProps' => [
                    'description' => $schedule->description,
                    'timeLabel' => $timeLabel,
                ],
            ];
        })->filter(fn($event) => !empty($event['daysOfWeek']))->values();

        return response()->json($events);
    }
}

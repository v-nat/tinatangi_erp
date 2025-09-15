<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    //
    public function thisDay()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->user_type == 'employee') {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found'
                ], 404);
            }

            $attendance = Attendance::where('employee_id', $user->id)
                ->whereDate('date', now())
                ->first();

            return response()->json([
                'success' => true,
                'data' => $attendance ? [
                    'time_in' => $attendance->time_in->format('H:i:s'),
                    'time_out' => $attendance->time_out?->format('H:i:s'),
                    'hours_worked' => $attendance->hours_worked
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error("Today attendance error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch today\'s attendance'
            ], 500);
        }
    }

    private function formatHours($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%dh %02dm', $hours, $mins);
    }

    private function formatMinutes($minutes)
    {
        return sprintf('%d minutes', $minutes);
    }

    public function timeIn()
    {
        $employee = Auth::user()->id;

        // Check if already timed in today
        $existing = Attendance::where('employee_id', $employee)
            ->whereDate('date', now())
            ->exists();

        if ($existing) {
            return back()->with('error', 'You already timed in today');
        }

        // Create new attendance record
        Attendance::create([
            'employee_id' => Auth::user()->id,
            'date' => now(),
            'time_in' => now(),
            'status' => 9 
        ]);

        return back()->with('success', 'Time in recorded');
    }
    public function timeOut()
    {
        $employee = Auth::user()->id;

        $attendance = Attendance::where('employee_id', $employee)
            ->whereDate('date', now())
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No time in found for today');
        }

        $attendance->update([
            'time_out' => now(),
            'hours_worked' => $attendance->time_in->diffInMinutes(now())
        ]);

        return back()->with('success', 'Time out recorded');
    }

    private function getStatusText($statusCode)
    {
        $statuses = [
            8 => '<span class="badge bg-success">Present</span>',
            9 => '<span class="badge bg-primary">On Time</span>',
            10 => '<span class="badge bg-warning">Late</span>',
            11 => '<span class="badge bg-danger">Absent</span>',
            null => '<span class="badge bg-secondary">Unknown</span>'
        ];
        return $statuses[$statusCode] ?? $statuses[null];
    }

    public function attendanceList()
    {
        try {
            $query = Attendance::with([
                'atEmployeeRS',
                'leaveRS',
                'overtimeRS'
            ])->orderBy('date', 'desc');
            $attendance = $query->get();

            $result = $attendance->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'employee_id' => $attendance->employee_id ?? 'N/A',
                    'name' => optional(optional($attendance->atEmployeeRS)->userRS)->full_name,
                    'date' => $attendance->date->format('Y-m-d'),
                    'time_in' => $attendance->time_in ? Carbon::parse($attendance->time_in)->format('h:i A') : 'N/A',
                    'time_out' => $attendance->time_out ? Carbon::parse($attendance->time_out)->format('h:i A') : 'N/A',
                    'total_minutes' => $attendance->hours_worked,
                    'status' => $this->getStatusText($attendance->status),
                    'tardiness' => $attendance->tardiness_minutes ? $this->formatMinutes($attendance->tardiness_minutes) : 'None',
                    'overtime' => $attendance->overtime_minutes ? $this->formatHours($attendance->overtime_minutes) : 'None',
                    'leave_info' => $attendance->leave
                        ? Carbon::parse($attendance->leave->start_date)->format('M j') . ' - ' .
                        Carbon::parse($attendance->leave->end_date)->format('M j, Y')
                        : 'N/A',
                ];
            });
            // dd($result);
            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            Log::error('Attendance list error: ' . $e->getMessage());
            return response()->json(["data" => []]);
        }
    }
}

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
            'status' => 4 // On Time status
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
            3 => '<span class="badge badge-success">Present</span>',
            4 => '<span class="badge badge-primary">On Time</span>',
            5 => '<span class="badge badge-warning">Late</span>',
            6 => '<span class="badge badge-danger">Absent</span>',
            null => '<span class="badge badge-secondary">Unknown</span>'
        ];
        return $statuses[$statusCode] ?? $statuses[null];
    }

    public function attendanceList()
    {
        try {
            $user = Auth::user();
            $employee = $user->id;

            $query = Attendance::with([
                'atEmployeeRS',
            ])->orderBy('date', 'desc');

            $attendance = $query->get();

            $result = $attendance->map(function ($record) {
                return [
                    'employee_id' => $record->employee_id ?? 'N/A',
                    // 'name' => trim(($record->employee->user->full_name ?? '')),
                    'date' => $record->date->format('Y-m-d'),
                    'time_in' => $record->time_in ? Carbon::parse($record->time_in)->format('h:i A') : 'N/A',
                    'time_out' => $record->time_out ? Carbon::parse($record->time_out)->format('h:i A') : 'N/A',
                    'hours_worked' => $record->hours_worked,
                    'tardiness' => $record->hours_late ? $this->formatMinutes($record->hours_late) : 'None',
                ];
            });
            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            Log::error('Attendance list error: ' . $e->getMessage());
            return response()->json(["data" => []]);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
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
                    'time_in' => optional($attendance->time_in)->format('H:i:s'),
                    'time_out' => optional($attendance->time_out)->format('H:i:s'),
                    'hours_worked' => $attendance->hours_worked
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function isOnLeave()
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
                ->where('is_leave', 1)
                ->first();

            return response()->json([
                'success' => true,
                'data' => $attendance ? [
                    'isLeave' => $attendance->is_leave
                ] : false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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

        $existing = Attendance::where('employee_id', $employee)
            ->whereDate('date', now())
            ->exists();

        $isOnLeave = optional($existing)->is_leave ?? false;

        if ($existing) {
            return back()->with('error', 'You already timed in today');
        }
        if ($isOnLeave) {
            return back()->with('error', "You're on Leave today");
        }

        // Create new attendance record
        Attendance::create([
            'employee_id' => Auth::user()->id,
            'date' => now(),
            'time_in' => now(),
            'status' => 7
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
            'hours_worked' => $attendance->time_in->diffInMinutes(now()),
            'status' => 6
        ]);

        return back()->with('success', 'Time out recorded');
    }

    public function attendanceList()
    {
        try {
            $attendances = Attendance::with([
                'atEmployeeRS',
                'leaveRS',
                'overtimeRS'
            ])
                ->whereDoesntHave('atEmployeeRS.position', function ($query) {
                    $query->where('level', 'ceo');
                })
                ->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->get();


            $result = $attendances->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'employee_id' => $attendance->employee_id ?? 'N/A',
                    'name' => optional(optional($attendance->atEmployeeRS)->userRS)->full_name,
                    'date' => $attendance->date->format('Y-m-d'),
                    'time_in' => $attendance->time_in ? Carbon::parse($attendance->time_in)->format('h:i A') : 'N/A',
                    'time_out' => $attendance->time_out ? Carbon::parse($attendance->time_out)->format('h:i A') : 'N/A',
                    'total_minutes' => $attendance->hours_worked,
                    'status' => Status::getStatusText($attendance->status),
                    'tardiness' => $attendance->tardiness_minutes ? $this->formatMinutes($attendance->tardiness_minutes) : 'None',
                    'overtime' => $attendance->overtime_minutes ? $this->formatHours($attendance->overtime_minutes) : 'None',
                    'leave_info' => $attendance->status == 8
                        ? Status::getStatusText(optional($attendance->leaveRS)->status)
                        : 'N/A',

                ];
            });

            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function employeeAttendanceList($id)
    {
        return view('pages.admin.human_resources.employee-attendance', compact('id'));
    }

    public function getEmployeeAttendanceList($id)
    {
        try {
            $attendances = Attendance::with([
                'atEmployeeRS',
                'leaveRS',
                'overtimeRS'
            ])->where('employee_id', $id)
                ->orderBy('date', 'desc')
                ->orderBy('time_in', 'desc')
                ->get();

            $result = $attendances->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'employee_id' => $attendance->employee_id ?? 'N/A',
                    'name' => optional(optional($attendance->atEmployeeRS)->userRS)->full_name,
                    'date' => $attendance->date->format('Y-m-d'),
                    'time_in' => $attendance->time_in ? Carbon::parse($attendance->time_in)->format('h:i A') : 'N/A',
                    'time_out' => $attendance->time_out ? Carbon::parse($attendance->time_out)->format('h:i A') : 'N/A',
                    'total_minutes' => $attendance->hours_worked,
                    'status' => Status::getStatusText($attendance->status),
                    'tardiness' => $attendance->tardiness_minutes ? $this->formatMinutes($attendance->tardiness_minutes) : 'None',
                    'overtime' => $attendance->overtime_minutes ? $this->formatHours($attendance->overtime_minutes) : 'None',
                    'leave_info' => $attendance->status == 8
                        ? Status::getStatusText(optional($attendance->leaveRS)->status)
                        : 'N/A',

                ];
            });

            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Schedule;
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

    // public function timeIn()
    // {
    //     $employee = Auth::user()->id;

    //     $existing = Attendance::where('employee_id', $employee)
    //         ->whereDate('date', now())
    //         ->exists();

    //     $isOnLeave = optional($existing)->is_leave ?? false;

    //     if ($existing) {
    //         return back()->with('error', 'You already timed in today');
    //     }
    //     if ($isOnLeave) {
    //         return back()->with('error', "You're on Leave today");
    //     }

    //     // Create new attendance record
    //     Attendance::create([
    //         'employee_id' => Auth::user()->id,
    //         'date' => now(),
    //         'time_in' => now(),
    //         'status' => 7
    //     ]);

    //     return back()->with('success', 'Time in recorded');
    // }



    // public function timeOut()
    // {
    //     $employee = Auth::user()->id;

    //     $attendance = Attendance::where('employee_id', $employee)
    //         ->whereDate('date', now())
    //         ->first();

    //     if (!$attendance) {
    //         return back()->with('error', 'No time in found for today');
    //     }

    //     $attendance->update([
    //         'time_out' => now(),
    //         'hours_worked' => $attendance->time_in->diffInMinutes(now()),
    //         'status' => 6
    //     ]);

    //     return back()->with('success', 'Time out recorded');
    // }



    public function timeIn()
    {
        try {
            $employeeId = Auth::user()->id;

            $existingAttendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('date', now())
                ->first();

            if ($existingAttendance) {
                if ($existingAttendance->is_leave) {
                    return response()->json(['success' => false, 'message' => "You are on leave today and cannot time in."], 422);
                }
                if ($existingAttendance->time_in) {
                    return response()->json(['success' => false, 'message' => 'You have already timed in today.'], 422);
                }
            }

            $todayDayOfWeek = now()->dayOfWeek;
            $schedule = Schedule::where('employee_id', $employeeId)
                ->whereJsonContains('days_of_week', (string) $todayDayOfWeek)
                ->first();

            dd($schedule);  

            if (!$schedule) {
                return response()->json(['success' => false, 'message' => 'You do not have a schedule for today.'], 422);
            }

            $now = now();

            $scheduledTimeIn = Carbon::parse(today()->toDateString() . ' ' . Carbon::parse($schedule->time_in)->format('H:i:s'));
            $tardiness = 0;
            $tardiness_minutes = 0;
            $status = 7;

            $gracePeriodTime = $scheduledTimeIn->copy()->addMinute();

            if ($now->isAfter($gracePeriodTime)) {
                $tardiness = 1;
                $tardiness_minutes = abs($now->diffInMinutes($scheduledTimeIn));
                $status = 9;
            }

            Attendance::create([
                'employee_id' => $employeeId,
                'date' => $now,
                'time_in' => $now,
                'status' => $status,
                'tardiness' => $tardiness,
                'tardiness_minutes' => $tardiness_minutes,
            ]);

            return response()->json(['success' => true, 'message' => 'Time in recorded successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function timeOut()
    {
        try {
            $employee = Auth::user()->id;
            $now = now();

            $attendance = Attendance::where('employee_id', $employee)
                ->whereDate('date', $now->toDateString())
                ->first();

            if (!$attendance) {
                return response()->json(['message' => 'No time in found for today'], 422);
            }

            if ($attendance->time_out) {
                return response()->json(['message' => 'You have already timed out today.'], 422);
            }

            $todayDayOfWeek = $now->dayOfWeek;
            $schedule = Schedule::where('employee_id', $employee)
                ->whereJsonContains('days_of_week', (string) $todayDayOfWeek)
                ->first();

            if (!$schedule) {
                $hours_worked = $attendance->time_in->diffInMinutes($now);
            } else {
                $scheduledTimeOut = Carbon::parse($now->toDateString() . ' ' . Carbon::parse($schedule->time_out)->format('H:i:s'));

                $timeForCalc = $now->isAfter($scheduledTimeOut) ? $scheduledTimeOut : $now;

                $hours_worked = $attendance->time_in->diffInMinutes($timeForCalc);
            }

            $dataToUpdate = [
                'time_out' => $now,
                'hours_worked' => $hours_worked,
            ];

            if ($attendance->status != 9) {
                $dataToUpdate['status'] = 6;
            }

            $attendance->update($dataToUpdate);

            return response()->json(['success' => true, 'message' => 'Time out recorded successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while timing out.'], 500);
        }
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
                ->whereDate('date', now())
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

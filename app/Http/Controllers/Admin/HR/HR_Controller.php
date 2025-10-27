<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\Schedule;
use App\Http\Controllers\Controller;

class HR_Controller extends Controller
{
    public function index()
    {
        $employees = Employee::with(['position'])->get();
        $totalActive = Employee::whereNull('deleted_at')->count();
        $overtimes = Overtime::where('status', 11)->count();
        $leaves = Leave::where('status', 11)->count();
        $newHires = Employee::whereNull('deleted_at')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->count();
        return view('pages.admin.human_resources.dashboard', compact('totalActive', 'newHires', 'overtimes', 'leaves'));
    }

    public function getSchedules()
    {
        $schedules = Schedule::with('employee.user')->get();
        $events = [];

        foreach ($schedules as $schedule) {
            if ($schedule->employee && $schedule->employee->user) {
                $events[] = [
                    'id'          => $schedule->id,

                    'resourceId'  => $schedule->employee_id,

                    'title'       => ($schedule->employee->user->first_name . ' ' . $schedule->employee->user->last_name),

                    'daysOfWeek'  => $schedule->days_of_week,
                    'startTime'   => $schedule->time_in,
                    'endTime'     => $schedule->time_out,
                    'color'       => $schedule->color,
                    'employee_id' => $schedule->employee_id,

                    'allDay'      => false 
                ];
            }
        }

        return response()->json($events);
    }

    public function employees()
    {
        return view('pages.admin.human_resources.employees');
    }
    public function getEmployees()
    {
        try {
            $employees = Employee::with(['user', 'user.statusRS', 'supervisor.user', 'position'])
            ->whereDoesntHave('position', function ($query) {
                $query->where('level', 'ceo');
            })
            ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'data' => $employees->map(function ($e) {
                    return [
                        'employee_id'       => $e->id,
                        'name'              => $e->user->full_name ?? 'N/A',
                        'position'          => $e->position->name ?? 'N/A',
                        'department'        => $e->deptRS->name ?? 'N/A',
                        'email'             => $e->user->email ?? 'N/A',
                        'direct_supervisor' => optional(optional($e->supervisor)->user)->full_name ?? 'Unassigned',
                        'status'            => $e->user->statusRS->status ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function otMngmnt()
    {
        return view('pages.admin.human_resources.ot-mngmnt');
    }

    public function otApplication($id)
    {
        return view('pages.admin.human_resources.ot-application', compact('id'));
    }
    public function leaveApplication($id)
    {
        return view('pages.admin.human_resources.leave-application', compact('id'));
    }
    public function leaveMngmnt()
    {
        return view('pages.admin.human_resources.leave-mngmnt');
    }

    public function employeePayslip($id)
    {
        return view('pages.admin.human_resources.employee-payslip', compact('id'));
    }
}

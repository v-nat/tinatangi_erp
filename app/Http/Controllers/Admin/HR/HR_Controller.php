<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\Schedule;
use App\Models\Attendance;
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
        $leaves = Attendance::with(['atEmployeeRS.userRS'])->where('is_leave', 1)
                           // Removed: ->whereBetween('date', [$start, $end])
                           ->get();

        foreach ($leaves as $leave) {
            $events[] = [
                'id'         => 'leave-' . $leave->id,
                'resourceId' => $leave->employee_id,
                'title'      => $leave->atEmployeeRS->userRS->full_name,
                'start'      => Carbon::parse($leave->date)->toDateString(),
                'allDay'     => true,
                // 'display'    => 'background',
                'color'      => '#dc3545',
                'textColor'  => '#fff'
            ];
        }

        return response()->json($events);
    }

    public function getDashboardAnalytics()
    {
        // --- Attendance status breakdown for the current week (Mon-Sun) ---
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        $weeklyAttendance = Attendance::whereBetween('date', [$weekStart, $weekEnd])
            ->whereDoesntHave('atEmployeeRS.position', fn($q) => $q->where('level', 'ceo'))
            ->selectRaw('DATE(date) as day, status, COUNT(*) as count')
            ->groupBy('day', 'status')
            ->get();

        $days = [];
        for ($d = $weekStart->copy(); $d->lte($weekEnd); $d->addDay()) {
            $days[] = $d->format('D');
        }

        // Map status codes: 6=On Time, 9=Late, 8=On Leave
        $onTime  = array_fill(0, 7, 0);
        $late    = array_fill(0, 7, 0);
        $onLeave = array_fill(0, 7, 0);

        foreach ($weeklyAttendance as $row) {
            $idx = Carbon::parse($row->day)->dayOfWeek; // 0=Sun,1=Mon...
            // Convert to Mon-based index
            $idx = ($idx + 6) % 7;
            if ($row->status == 6)      $onTime[$idx]  += $row->count;
            elseif ($row->status == 9)  $late[$idx]    += $row->count;
            elseif ($row->status == 8)  $onLeave[$idx] += $row->count;
        }

        // --- Department headcount ---
        $deptBreakdown = Employee::whereNull('deleted_at')
            ->whereDoesntHave('position', fn($q) => $q->where('level', 'ceo'))
            ->with('deptRS')
            ->get()
            ->groupBy(fn($e) => optional($e->deptRS)->name ?? 'Unassigned')
            ->map->count()
            ->sortDesc();

        // --- Monthly overtime requests (last 6 months) ---
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $overtimeTrend = Overtime::where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $otLabels = [];
        $otCounts = [];
        for ($m = 0; $m < 6; $m++) {
            $label = Carbon::now()->subMonths(5 - $m)->format('M Y');
            $key   = Carbon::now()->subMonths(5 - $m)->format('Y-m');
            $otLabels[] = $label;
            $match = $overtimeTrend->firstWhere('month', $key);
            $otCounts[] = $match ? (int) $match->count : 0;
        }

        // --- Payroll net pay per month (last 6 months) ---
        $payrollTrend = Payroll::where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(net_pay) as total")
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month')
            ->get();

        $payrollLabels = $otLabels;
        $payrollTotals = [];
        for ($m = 0; $m < 6; $m++) {
            $key   = Carbon::now()->subMonths(5 - $m)->format('Y-m');
            $match = $payrollTrend->firstWhere('month', $key);
            $payrollTotals[] = $match ? (float) $match->total : 0;
        }

        return response()->json([
            'weekly_attendance' => [
                'days'     => $days,
                'on_time'  => $onTime,
                'late'     => $late,
                'on_leave' => $onLeave,
            ],
            'department_headcount' => [
                'labels' => $deptBreakdown->keys()->values(),
                'counts' => $deptBreakdown->values(),
            ],
            'overtime_trend' => [
                'labels' => $otLabels,
                'counts' => $otCounts,
            ],
            'payroll_trend' => [
                'labels' => $payrollLabels,
                'totals' => $payrollTotals,
            ],
        ]);
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

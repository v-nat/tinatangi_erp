<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollRequest;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Status;
use App\Services\CompensationCalculator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class PayrollController extends Controller
{

    public function __construct(
        protected CompensationCalculator $calculator
    ) {}
    // for views

    public function indexOnHr()
    {
        return view("pages.admin.human_resources.payroll");
    }

    public function getPayrollList()
    {

        try {
            $query = Payroll::with([
                'employee',
            ]);
            $payroll = $query->get();

            $result = $payroll->map(function ($payroll) {
                return [
                    'id' => $payroll->id,
                    'employee_id' => $payroll->employee_id ?? 'N/A',
                    'name' => optional(optional($payroll->employee)->userRS)->full_name,
                    'department' => optional(optional($payroll->employee)->deptRS)->name,
                    'position' => optional(optional($payroll->employee)->position)->name ?? 'N/A',
                    'period' => $this->getMonthString($payroll->month) ?? '',
                    'reg_pay' => $payroll->regular_hour_pay ?? '',
                    'gross_pay' => $payroll->gross_pay ?? '',
                    'gross_deduction' => $payroll->deduction + $payroll->days_absent_deduction ?? '',
                    'net_pay' => $payroll->net_pay ?? '',
                    'status' => Status::getStatusText($payroll->status),
                ];
            });
            // dd($result);
            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            Log::error('Attendance list error: ' . $e->getMessage());
            return response()->json(["data" => []]);
        }
    }

    public function getMonthString($month)
    {
        switch ($month) {
            case 1:
                return "January";
            case 2:
                return "February";
            case 3:
                return "March";
            case 4:
                return "April";
            case 5:
                return "May";
            case 6:
                return "June";
            case 7:
                return "July";
            case 8:
                return "August";
            case 9:
                return "September";
            case 10:
                return "October";
            case 11:
                return "Nomvember";
            case 12:
                return "December";
        }
    }
















    // VARIABLES 
    const WORKING_DAYS_PER_MONTH = 26;
    const DAY_OFF_PER_MONTH = 4;
    const WORKING_HOURS_PER_DAY = 8;
    const OVERTIME_RATE_MULTIPLIER = 1.25;

    // FUNCTIONS
    public function generatePayroll(StorePayrollRequest $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|integer|exists:employees,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()->all()
                ], 422);
            }

            $employee = Employee::findOrFail($request->employee_id);
            $payroll_start_date = Carbon::parse($request->start_date);
            $payroll_end_date = Carbon::parse($request->end_date);

            $data = $this->initialComputation($employee, $payroll_start_date, $payroll_end_date);
            $breakdown = $this->calculator->fromPayrollAttributes($data);

            $payroll = Payroll::create(array_merge($data, $breakdown));
            $payroll->save();

            DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payroll Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Payroll generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function initialComputation($employee, $start_date, $end_date)
    {
        ///// for calculation
        $per_hour_rate = $this->ratePerHour($employee->base_salary);
        $working_days = $this->getTotalWorkingDays($start_date, $end_date);
        $daily_rate = $employee->base_salary / $working_days;

        ///// to pass for computation

        ///// pay
        $days_present = $this->getTotalPresentDays($employee->id, $start_date, $end_date);
        $total_hours_worked = $this->totalHoursWorked($employee->id, $start_date, $end_date);
        $regular_pay = $this->regularPay($employee, $working_days, $days_present);
        $overtime_pay = $this->overtimePay($employee, $start_date, $end_date, $per_hour_rate);

        ///// deduct
        $days_absent = $this->absencesTotal($employee->id, $start_date, $end_date, $working_days);
        $days_absent_deduction = $this->absentDeduction($days_absent, $daily_rate);
        $tardiness_total = $this->tardinessTotal($employee->id, $start_date, $end_date);
        $tardiness_deduction = $this->tardinessDeduction($employee->id, $start_date, $end_date, $per_hour_rate);
        $mandatory_deduction = $this->totalMandatoryDeductions();

        return [
            'days_present' => $days_present,
            'total_hours_worked' => $total_hours_worked,
            'regular_hour_pay' => $regular_pay,
            'overtime_pay' => $overtime_pay,
            'days_absent' => $days_absent,
            'days_absent_deduction' => $days_absent_deduction,
            'tardiness_deduction' => $tardiness_deduction,
            'mandatory_deduction' => $mandatory_deduction,
            'deduction' => $mandatory_deduction['total'],

            'per_hour_rate' => $per_hour_rate,
            'daily_rate' => $daily_rate,
            'working_days' => $working_days,
            'tardiness_total' => $tardiness_total,

            'employee_id' => $employee->id,
            'month' => Carbon::parse($start_date)->format('m'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payroll_date' => now(),
            'status' => 7,
        ];
    }


    protected function ratePerHour($base_salary)
    {
        return $base_salary / (self::WORKING_DAYS_PER_MONTH * self::WORKING_HOURS_PER_DAY);
    }
    protected function getTotalPresentDays($employeeID, $start_date, $end_date)
    {
        return Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date, $end_date])
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->distinct('date')
            ->count('date');
    }

    protected function totalHoursWorked($employeeID, $start_date, $end_date)
    {
        $minutes = Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date, $end_date])
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->sum('hours_worked');

        return $minutes / 60;
    }

    protected function regularPay($employee, $working_days, $days_present)
    {
        /////// whole month
        if ($working_days >= self::WORKING_DAYS_PER_MONTH) {
            return ($days_present / self::WORKING_DAYS_PER_MONTH) * $employee->base_salary;
        }
        ////// half month
        $dailyRate = $employee->base_salary / self::WORKING_DAYS_PER_MONTH;
        return $days_present * $dailyRate;
    }

    protected function getTotalWorkingDays($startDate, $endDate): int
    {
        // Ensure Carbon instances
        $start = $startDate instanceof Carbon
            ? $startDate
            : Carbon::parse($startDate);

        $end = $endDate instanceof Carbon
            ? $endDate
            : Carbon::parse($endDate);

        // Full days in period
        $daysInPeriod = $start->diffInDays($end) + 1;

        // Days in that month
        $daysInMonth = $start->daysInMonth;

        // Configurable working days
        $standardDays = Config::get('payroll.working_days_per_month', 26);

        // Prorate and round
        return (int) round($daysInPeriod / $daysInMonth * $standardDays);
    }

    protected function overtimePay($employee, $start_date, $end_date, $per_hour)
    {
        $overtimeRate = $per_hour * self::OVERTIME_RATE_MULTIPLIER;
        $overtimeHours = $this->overtimeHours($employee->id, $start_date, $end_date);
        return $overtimeHours * $overtimeRate;
    }
    protected function overtimeHours($employeeID, $start_date, $end_date)
    {
        $minutes = Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date, $end_date])
            ->sum('overtime_minutes');

        return $minutes;
    }

    protected function absencesTotal($employeeID, $start_date, $end_date, $working_days)
    {
        // 1) Fetch all the dates this employee was present (Y-m-d strings)
        $presentDays = Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->toArray();

        // 2) Determine how many working days to expect
        if (empty($working_days) || $working_days < 1) {
            // Count actual weekdays in the range if no override provided
            $period = CarbonPeriod::create($start_date, '1 day', $end_date);
            $working_days = collect($period)
                ->filter(fn(Carbon $date) => $date->isWeekday())
                ->count();
        }

        // 3) Number of days actually present (but never more than $working_days)
        $presentCount = min(count($presentDays), $working_days);

        // 4) Absent days = expected working days minus days present
        return max($working_days - $presentCount, 0);
    }

    protected function absentDeduction($days_absent, $daily_rate)
    {
        return $days_absent * $daily_rate;
    }

    protected function tardinessDeduction($employee_id, $start_date, $end_date, $hourlyRate)
    {
        $tardinessHours = $this->tardinessTotal($employee_id, $start_date, $end_date) / 60;
        return $tardinessHours * $hourlyRate;
    }

    protected function tardinessTotal($employeeID, $start_date, $end_date)
    {
        return Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date, $end_date])
            ->sum('tardiness_minutes');
    }

    protected function totalMandatoryDeductions()
    {
        $sss = 600;
        $philhealth = 450;
        $pagibig = 100;
        $total = $sss + $philhealth + $pagibig;
        return [
            'sss' => $sss,
            'philhealth' => $philhealth,
            'pagibig' => $pagibig,
            'total' => $total
        ];
    }
}

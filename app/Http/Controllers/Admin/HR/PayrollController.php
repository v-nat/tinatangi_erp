<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollRequest;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PayrollController extends Controller
{

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
                    'position' => optional($payroll->employee)->position,
                    'period' => $this->getMonthString($payroll->month) ?? '',
                    'reg_pay' => $payroll->regular_hour_pay ?? '',
                    'gross_pay' => $payroll->gross_pay ?? '',
                    'gross_deduction' => $payroll->deduction ?? '',
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
    const SSS = 600;
    const PHILHEALTH = 450;
    const PAGIBIG = 100;

    const WORKING_DAYS_PER_MONTH = 26;
    const WORKING_HOURS_PER_DAY = 8;
    const WORKING_PAY_PER_DAY = 800;
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

            $data = $this->overallComputation($employee, $payroll_start_date, $payroll_end_date);
            // dd  ($data);
            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'month' => Carbon::parse($request->start_date)->format('m'),
                'start_date' => $payroll_start_date,
                'end_date' => $payroll_end_date,
                'payroll_date' => now(),
                'days_present' => $data['days_present'],
                'total_hours_worked' => $data['total_hours'],
                'regular_hour_pay' => $data['regular_pay'],
                'overtime_pay' => $data['overtime_pay'],
                'days_absent' => $data['days_absent'],
                'days_absent_deduction' => $data['absent_deduction'],
                'tardiness_deduction' => $data['tardiness_deduction'],
                'deduction' => $data['mandatory_deductions']['total'] ?? 0,
                'tax_deduction' => $data['tax'],
                'gross_pay' => $data['gross_pay'],
                'salary_before_tax' => $data['gross_pay'] -
                    $data['absent_deduction'] -
                    $data['tardiness_deduction'],
                'net_pay' => $data['net_pay'],
                'status' => 7
            ]);

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
        if ($working_days >= self::WORKING_DAYS_PER_MONTH) {
            return ($days_present / self::WORKING_DAYS_PER_MONTH) * $employee->salary;
        }
        // 15's  30's
        // $dailyRate = $employee->salary / self::WORKING_DAYS_PER_MONTH;
        $dailyRate = 800;
        return $days_present * $dailyRate;
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

    protected function absencesTotal($employeeID, $start_date, $end_date)
    {
        // Get all dates the employee was present
        $present_days = Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date, $end_date])
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->pluck('date')
            ->toArray();

        $present_days = array_map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        }, $present_days);

        $absents = 0;
        $current = $start_date->copy();

        while ($current <= $end_date) {
            if ($current->isWeekday() && !in_array($current->format('Y-m-d'), $present_days)) {
                $absents++;
            }
            $current->addDay();
        }
        return $absents;
    }

    protected function absentDeduction($days_absent, $daily_rate)
    {
        return $days_absent * $daily_rate;
    }

    protected function tardinessDeduction($employee, $start_date, $end_date, $hourlyRate)
    {
        $tardinessHours = $this->tardinessTotal($employee->id, $start_date, $end_date) / 60;
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
        $sss = self::SSS;

        $philhealth = self::PHILHEALTH;

        $pagibig = self::PAGIBIG;

        return [
            'sss' => $sss,
            'philhealth' => $philhealth,
            'pagibig' => $pagibig,
            'total' => $sss + $philhealth + $pagibig
        ];
    }

    protected function taxableIncome($grossPay, $absentDeduction, $tardinessDeduction, $mandatoryDeductions)
    {
        return $grossPay - $absentDeduction - $tardinessDeduction - $mandatoryDeductions;
    }

    protected function taxAmount($taxableIncome)
    {
        if ($taxableIncome <= 20833) {
            return 0;
        } elseif ($taxableIncome <= 33332) {
            return ($taxableIncome - 20833) * 0.20;
        } elseif ($taxableIncome <= 66666) {
            return 2500 + ($taxableIncome - 33333) * 0.25;
        } elseif ($taxableIncome <= 166666) {
            return 10833.33 + ($taxableIncome - 66667) * 0.30;
        } elseif ($taxableIncome <= 666666) {
            return 40833.33 + ($taxableIncome - 166667) * 0.32;
        } else {
            return 200833.33 + ($taxableIncome - 666667) * 0.35;
        }
    }

    protected function getTotalWorkingDays($startDate, $endDate): int
    {
        $totalDays = $startDate->diffInDaysFiltered(function ($date) {
            return true; // include all days
        }, $endDate) + 1;

        $weeks = floor($totalDays / 7);
        return $totalDays - $weeks;
    }

    protected function overallComputation($employee, $startDate, $endDate)
    {
        $salary = $employee->salary;
        // $hourlyRate = $this->calculateHourlyRate($salary);
        $dailyRate = self::WORKING_PAY_PER_DAY;
        $workingDays = $this->getTotalWorkingDays($startDate, $endDate);
        $daysPresent = $this->getTotalPresentDays($employee->id, $startDate, $endDate);
        $regularPay = $this->regularPay($employee, $workingDays, $daysPresent);
        $overtimePay = $this->overtimePay($employee, $startDate, $endDate, $salary);
        $daysAbsent = $this->absencesTotal($employee->id, $startDate, $endDate);
        $absentDeduction = $this->absentDeduction($daysAbsent, $dailyRate);
        $tardinessDeduction = $this->tardinessDeduction($employee, $startDate, $endDate, $salary);
        $mandatoryDeductions = $this->totalMandatoryDeductions();

        $grossPay = $regularPay + $overtimePay;
        $taxableIncome = $this->taxableIncome(
            $grossPay,
            $absentDeduction,
            $tardinessDeduction,
            $mandatoryDeductions['total']
        );
        $tax = $this->taxAmount($taxableIncome);

        $grossDeduction = $absentDeduction +
            $tardinessDeduction +
            $mandatoryDeductions['total'] +
            $tax;
        $netPay = $grossPay - $grossDeduction;

        return [
            'days_present' => $daysPresent,
            'days_absent' => $daysAbsent,
            'total_hours' => $this->totalHoursWorked($employee->id, $startDate, $endDate),
            'overtime_hours' => $this->overtimeHours($employee->id, $startDate, $endDate),
            'tardiness_minutes' => $this->tardinessTotal($employee->id, $startDate, $endDate),
            'regular_pay' => $regularPay,
            'overtime_pay' => $overtimePay,
            'absent_deduction' => $absentDeduction,
            'tardiness_deduction' => $tardinessDeduction,
            'mandatory_deductions' => $mandatoryDeductions,
            'tax' => $tax,
            'gross_pay' => $grossPay,
            'total_deductions' => $grossDeduction,
            'net_pay' => max($netPay, 0)
        ];
    }






    //
}

<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Payroll;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\BudgetRelease;
use App\Models\PayrollSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\AuthController;
use App\Services\CompensationCalculator;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Controllers\GenerateIdController;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreBatchPayrollRequest;

class PayrollController extends Controller
{
    public function __construct(
        protected CompensationCalculator $calculator
    ) {}

    public function indexOnHr()
    {
        return view("pages.admin.human_resources.payroll");
    }
    public function indexOnFinance()
    {
        return view("pages.admin.finance.finance-payroll");
    }

    public function getPayrollList()
    {

        try {
            $query = Payroll::with([
                'employee',
            ])->orderBy('updated_at', 'desc');
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

            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function getPayslipList($id)
    {
        try {
            $query = Payroll::with([
                'employee',
            ])->where('employee_id', $id)
                ->where('status', 15)
                ->orderBy('updated_at', 'desc');
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

            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function getPayrollView($id)
    {

        try {
            $payroll = Payroll::findOrFail($id);
            $payrollSettings = PayrollSettings::where('status', 1)->first();

            $remarks = null;
            if ($payroll->remarks == 'payroll released') {
                $remarks = '<div class="alert alert-success">' . \Illuminate\Support\Str::upper($payroll->remarks) . '</div>';
            } else if ($payroll->remarks == 'budget released') {
                $remarks = '<div class="alert alert-success">' . \Illuminate\Support\Str::upper($payroll->remarks) . '</div>';
            } else if ($payroll->remarks == 'requesting budget') {
                $remarks = '<div class="alert alert-info">' . \Illuminate\Support\Str::upper($payroll->remarks) . '</div>';
            } else if ($payroll->remarks) {
                $remarks = '<div class="alert alert-danger"> Rejected: ' . $payroll->remarks . '</div>';
            }

            $result = [
                'id' => $payroll->id,
                'employee_id' => $payroll->employee_id ?? 'N/A',
                'name' => optional(optional($payroll->employee)->userRS)->full_name,
                'department' => optional(optional($payroll->employee)->deptRS)->name,
                'position' => optional(optional($payroll->employee)->position)->name ?? 'N/A',
                'start_date' => optional(Carbon::parse($payroll->start_date))->format('M d, Y'),
                'end_date' => optional(Carbon::parse($payroll->end_date))->format('M d, Y'),
                'working_days' => $this->getTotalWorkingDays($payroll->start_date, $payroll->end_date),
                'period' => $this->getMonthString($payroll->month) ?? '',
                'payroll_date' => $payroll->payroll_date ?? '',
                'days_present' => $payroll->days_present,
                'days_absent' => $payroll->days_absent,
                'reg_pay' => $payroll->regular_hour_pay ?? '',
                'total_hours' => $payroll->total_hours_worked ?? '',
                'overtime_pay' => $payroll->overtime_pay ?? '',
                'leave_pay' => $payroll->leave_pay ?? '',
                'gross_pay' => $payroll->gross_pay ?? '',
                'absent_deduction' => $payroll->days_absent_deduction ?? '',
                'tardiness_deduction' => $payroll->tardiness_deduction ?? '',
                'sss' => $payroll->sss ?? 0,
                'philhealth' => $payroll->philhealth ?? 0,
                'pagibig' => $payroll->pagibig ?? 0,
                'mandatory_deduction' => $payroll->deduction,
                'tax_deduction' => $payroll->tardiness_deduction,
                'salary_before_tax' => $payroll->salary_before_tax,
                'gross_deduction' => $payroll->deduction + $payroll->days_absent_deduction ?? '',
                'net_pay' => $payroll->net_pay ?? '',
                'remarks' => $remarks ?? '',
                'status' => Status::getStatusText($payroll->status),
            ];

            return response()->json(["data" => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function putOnProcess(Request $request, $id, $status)
    {
        try {
            DB::beginTransaction();

            $payroll = Payroll::findOrFail($id);

            if ($request->remarks) {
                $payroll->remarks = $request->remarks;
                $payroll->status = $status;
                $payroll->save();
            } else if ($status != 12) {
                $payroll->remarks = 'requesting budget';
                $payroll->status = $status;
                $payroll->save();
            }

            if ($status == 14) {
                $year = Carbon::now()->format('Y');
                $release_id = GenerateIdController::generateID('release_id');

                $release = BudgetRelease::create([
                    'release_id'        => $release_id,
                    'type'              => 'Payroll',
                    'amount'            => $payroll->net_pay,
                    'request_id'        => $id,
                    'requested_by_id'   => auth('')->user()->id,
                    'requested_at'      => now(),
                    'released_by_id'    => null,
                    'released_at'       => null,
                    'department'        => 2,
                    'notes'             => 'requesting budget',
                    'status'            => 11,
                ]);

                $release->save();
                DB::commit();
                return response()->json(['success' => true, 'message' => 'Payroll is now on Process!'], 200);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payroll Request Rejected!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function releasePayroll($id)
    {
        try {
            DB::beginTransaction();

            $payroll = Payroll::find($id);

            if (auth('')->user()->id == $payroll->employee_id || !AuthController::checkAuthorization()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }
            $payroll->remarks = 'payroll released';
            $payroll->status = 15;
            $payroll->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Payroll Released Successfully!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function batchReleasePayroll(Request $request)
    {
        try {
            DB::beginTransaction();

            if (!AuthController::checkAuthorization()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }

            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:payrolls,id'
            ]);

            $ids = $request->ids;
            $payrolls = Payroll::whereIn('id', $ids)
                ->where('status', 13)
                ->get();

            if ($payrolls->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No approved payrolls found to release.'
                ], 400);
            }

            $releasedCount = 0;
            foreach ($payrolls as $payroll) {
                if (auth('')->user()->id != $payroll->employee_id) {
                    $payroll->remarks = 'payroll released';
                    $payroll->status = 15;
                    $payroll->save();
                    $releasedCount++;
                } else if (auth('')->user()->id == $payroll->employee_id || !AuthController::checkAuthorization()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized for this action.'
                    ], 401);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully released {$releasedCount} payroll(s)!"
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
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
                return "November";
            case 12:
                return "December";
        }
    }

    const WORKING_DAYS_PER_MONTH = 26;
    const DAY_OFF_PER_MONTH = 4;
    const WORKING_HOURS_PER_DAY = 8;
    const OVERTIME_RATE_MULTIPLIER = 1.25;

    public function generatePayroll(StorePayrollRequest $request)
    {
        try {
            DB::beginTransaction();

            $payrollSettings = PayrollSettings::where('status', 1)->first();
            if (!$payrollSettings) {
                DB::rollBack();
                return response()->json(['error' => 'Active payroll settings not found. Please configure payroll settings first.'], 404);
            }

            $validated = $request->validated();

            $employee = Employee::findOrFail($validated['employee_id']);
            $payroll_start_date = Carbon::parse($validated['start_date']);
            $payroll_end_date = Carbon::parse($validated['end_date']);

            $data = $this->initialComputation($employee, $payroll_start_date, $payroll_end_date, $payrollSettings);
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

    public function generateBatchPayroll(StoreBatchPayrollRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $payrollSettings = PayrollSettings::where('status', 1)->first();
            if (!$payrollSettings) {
                DB::rollBack();
                return response()->json(['error' => 'Active payroll settings not found. Please configure payroll settings first.'], 404);
            }

            $payroll_start_date = Carbon::parse($validated['start_date']);
            $payroll_end_date = Carbon::parse($validated['end_date']);

            $employeeIds = $validated['employee_ids'];

            foreach ($employeeIds as $employeeId) {
                $employee = Employee::findOrFail($employeeId);

                $data = $this->initialComputation($employee, $payroll_start_date, $payroll_end_date, $payrollSettings);
                $breakdown = $this->calculator->fromPayrollAttributes($data);

                $payroll = Payroll::create(array_merge($data, $breakdown));
                $payroll->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Batch payroll for ' . count($employeeIds) . ' employees generated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch Payroll Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Batch payroll generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function initialComputation($employee, $start_date, $end_date, PayrollSettings $payrollSettings)
    {
        $per_hour_rate = $this->ratePerHour($employee->base_salary);
        $working_days = $this->getTotalWorkingDays($start_date, $end_date);
        $daily_rate = $employee->base_salary / $working_days;

        $days_present = $this->getTotalPresentDays($employee->id, $start_date, $end_date);
        $total_hours_worked = $this->totalHoursWorked($employee->id, $start_date, $end_date);
        $regular_pay = $this->regularPay($employee, $working_days, $days_present);
        $overtime_pay = $this->overtimePay($employee, $start_date, $end_date, $per_hour_rate);
        $leave_pay = $this->leavePay($employee->id, $start_date, $end_date, $daily_rate, $working_days);

        $days_absent = $this->absencesTotal($employee->id, $start_date, $end_date, $working_days);
        $days_absent_deduction = $this->absentDeduction($days_absent, $daily_rate);
        $tardiness_total = $this->tardinessTotal($employee->id, $start_date, $end_date);
        $tardiness_deduction = $this->tardinessDeduction($employee->id, $start_date, $end_date, $per_hour_rate);
        $mandatory_deduction = $this->totalMandatoryDeductions($payrollSettings);

        return [
            'days_present' => $days_present,
            'total_hours_worked' => $total_hours_worked,
            'regular_hour_pay' => $regular_pay,
            'overtime_pay' => $overtime_pay,
            'leave_pay' => $leave_pay,

            'days_absent' => $days_absent,
            'days_absent_deduction' => $days_absent_deduction,
            'tardiness_deduction' => $tardiness_deduction,
            'mandatory_deduction' => $mandatory_deduction,
            'deduction' => $mandatory_deduction['total'],
            'sss' => $mandatory_deduction['sss'],
            'philhealth' => $mandatory_deduction['philhealth'],
            'pagibig' => $mandatory_deduction['pagibig'],

            'per_hour_rate' => $per_hour_rate,
            'daily_rate' => $daily_rate,
            'working_days' => $working_days,
            'tardiness_total' => $tardiness_total,

            'employee_id' => $employee->id,
            'month' => Carbon::parse($start_date)->format('m'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payroll_date' => now(),
            'status' => 11,
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
        if ($working_days >= self::WORKING_DAYS_PER_MONTH) {
            return ($days_present / self::WORKING_DAYS_PER_MONTH) * $employee->base_salary;
        }
        $dailyRate = $employee->base_salary / self::WORKING_DAYS_PER_MONTH;
        return $days_present * $dailyRate;
    }

    protected function getTotalWorkingDays($startDate, $endDate): int
    {
        $start = $startDate instanceof Carbon
            ? $startDate
            : Carbon::parse($startDate);

        $end = $endDate instanceof Carbon
            ? $endDate
            : Carbon::parse($endDate);

        $daysInPeriod = $start->diffInDays($end) + 1;

        $daysInMonth = $start->daysInMonth;

        $standardDays = Config::get('payroll.working_days_per_month', 26);

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

        return $minutes / 60;
    }

    protected function leavePay($employeeID, $start_date, $end_date, $daily_rate, $working_days)
    {
        $daysLeave = Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
            ->where('is_leave', 1)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->toArray();

        if (empty($working_days) || $working_days < 1) {
            $period = CarbonPeriod::create($start_date, '1 day', $end_date);
            $working_days = collect($period)
                ->filter(fn(Carbon $date) => $date->isWeekday())
                ->count();
        }
        $leaveCount = min(count($daysLeave), $working_days);

        return $leaveCount * $daily_rate;
    }

    protected function absencesTotal($employeeID, $start_date, $end_date, $working_days)
    {
        $presentDays = Attendance::where('employee_id', $employeeID)
            ->whereBetween('date', [$start_date->toDateString(), $end_date->toDateString()])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->toArray();

        if (empty($working_days) || $working_days < 1) {
            $period = CarbonPeriod::create($start_date, '1 day', $end_date);
            $working_days = collect($period)
                ->filter(fn(Carbon $date) => $date->isWeekday())
                ->count();
        }
        $presentCount = min(count($presentDays), $working_days);

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

    protected function totalMandatoryDeductions(PayrollSettings $payrollSettings)
    {
        $sss = $payrollSettings->sss;
        $philhealth = $payrollSettings->philhealth;
        $pagibig = $payrollSettings->pagibig;
        $total = $sss + $philhealth + $pagibig;
        return [
            'sss' => $sss,
            'philhealth' => $philhealth,
            'pagibig' => $pagibig,
            'total' => $total
        ];
    }
}

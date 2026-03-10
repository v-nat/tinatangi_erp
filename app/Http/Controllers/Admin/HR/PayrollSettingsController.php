<?php

namespace App\Http\Controllers\Admin\HR;

use App\Models\Employee;
use App\Models\Position;
use App\Mail\SalaryUpdated;
use App\Mail\ContributionUpdated;
use App\Models\ContributionRate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\EmployeeSalarySettings;
use App\Http\Controllers\AuthController;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdateSalarySettingRequest;
use App\Http\Requests\UpdatePayrollSettingsRequest;

class PayrollSettingsController extends Controller
{
    public function updatePayrollSettings(UpdatePayrollSettingsRequest $request)
    {
        if (!AuthController::checkAuthorization()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized for this action.'
            ], 401);
        }
        try {
            $validatedData = $request->validated();

            DB::transaction(function () use ($validatedData) {
                ContributionRate::where('is_active', 1)->update(['is_active' => 0]);

                ContributionRate::create([
                    'sss_employee_rate'        => $validatedData['sss_employee_rate'],
                    'sss_employer_rate'        => $validatedData['sss_employer_rate'],
                    'philhealth_employee_rate' => $validatedData['philhealth_employee_rate'],
                    'philhealth_employer_rate' => $validatedData['philhealth_employer_rate'],
                    'pagibig_employee_rate'    => $validatedData['pagibig_employee_rate'],
                    'pagibig_employer_rate'    => $validatedData['pagibig_employer_rate'],
                    'effective_date'           => now()->toDateString(),
                    'is_active'                => 1,
                    'created_by'               => auth('')->id(),
                ]);
            });

            $employees = Employee::with('user:id,email')->get();
            foreach ($employees as $employee) {
                if ($employee->user && $employee->user->email) {
                    try {
                        Mail::to($employee->user->email)->send(new ContributionUpdated($validatedData));
                    } catch (\Exception $e) {
                        return response()->json(['message' => $e->getMessage()], 500);
                    }
                }
            }

            return response()->json(['message' => 'Contribution rates updated and notifications are being sent!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getPayrollSettings()
    {
        $rate = ContributionRate::where('is_active', 1)->first();

        if (!$rate) {
            return response()->json([
                'sss_employee_rate'        => 0,
                'sss_employer_rate'        => 0,
                'philhealth_employee_rate' => 0,
                'philhealth_employer_rate' => 0,
                'pagibig_employee_rate'    => 0,
                'pagibig_employer_rate'    => 0,
            ]);
        }

        return response()->json($rate);
    }

    public function getContributionRateHistory()
    {
        $rates = ContributionRate::withTrashed()
            ->with('creator:id,first_name,last_name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rate) {
                return [
                    'id'                       => $rate->id,
                    'sss_employee_rate'        => $rate->sss_employee_rate,
                    'sss_employer_rate'        => $rate->sss_employer_rate,
                    'philhealth_employee_rate' => $rate->philhealth_employee_rate,
                    'philhealth_employer_rate' => $rate->philhealth_employer_rate,
                    'pagibig_employee_rate'    => $rate->pagibig_employee_rate,
                    'pagibig_employer_rate'    => $rate->pagibig_employer_rate,
                    'effective_date'           => $rate->effective_date?->format('M d, Y'),
                    'is_active'                => $rate->is_active,
                    'set_by'                   => $rate->creator
                        ? $rate->creator->first_name . ' ' . $rate->creator->last_name
                        : 'System',
                    'created_at'               => $rate->created_at?->format('M d, Y h:i A'),
                ];
            });

        return response()->json(['data' => $rates]);
    }

    public function getSingleSalarySetting($id)
    {
        try {
            $salarySetting = EmployeeSalarySettings::with('position:id,name')->findOrFail($id);
            return response()->json($salarySetting);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Salary setting not found.'], 404);
        }
    }

    const WORKING_DAYS_PER_MONTH = 26;
    const WORKING_HOURS_PER_DAY = 8;

    public function updateSalarySetting(UpdateSalarySettingRequest $request, $id)
    {
        if (!AuthController::checkAuthorization()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized for this action.'
            ], 401);
        }
        try {
            $validatedData = $request->validated();
            $baseSalary = $validatedData['base_salary'];

            $ratePerDay = $baseSalary / self::WORKING_DAYS_PER_MONTH;
            $ratePerHour = $ratePerDay / self::WORKING_HOURS_PER_DAY;

            $salarySetting = EmployeeSalarySettings::findOrFail($id);

            $affectedEmployees = Employee::where('position_id', $salarySetting->position_id)->get();

            DB::transaction(function () use ($salarySetting, $baseSalary, $ratePerHour, $ratePerDay) {
                $salarySetting->update([
                    'base_salary' => $baseSalary,
                    'rate_per_hour' => round($ratePerHour, 2),
                    'rate_per_day' => round($ratePerDay, 2),
                ]);

                if ($salarySetting->position_id) {
                    Employee::where('position_id', $salarySetting->position_id)->update(['base_salary' => $baseSalary]);
                }
            });

            foreach ($affectedEmployees as $employee) {
                if ($employee->user && $employee->user->email) {
                     try {
                        Mail::to($employee->user->email)->send(new SalaryUpdated(
                            $employee->user->first_name,
                            $salarySetting->position->name,
                            $baseSalary
                        ));
                    } catch (\Exception $e) {
                        return response()->json(['message' => $e->getMessage()], 500);
                    }
                }
            }

            return response()->json(['message' => 'Salary updated and notifications are being sent to affected employees!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function storePositionAndSalary(StorePositionRequest $request)
    {
        if (!AuthController::checkAuthorization()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized for this action.'
            ], 401);
        }
        try {
            $validatedData = $request->validated();
            $baseSalary = $validatedData['base_salary'];

            $ratePerDay = 0;
            $ratePerHour = 0;
            if ($baseSalary > 0 && self::WORKING_DAYS_PER_MONTH > 0 && self::WORKING_HOURS_PER_DAY > 0) {
                $ratePerDay = $baseSalary / self::WORKING_DAYS_PER_MONTH;
                $ratePerHour = $ratePerDay / self::WORKING_HOURS_PER_DAY;
            }

            $newPosition = DB::transaction(function () use ($validatedData, $baseSalary, $ratePerHour, $ratePerDay) {
                $position = Position::create([
                    'name' => $validatedData['name'],
                    'department_id' => $validatedData['department_id'],
                    'level' => $validatedData['level'],
                    'status' => 1,
                ]);

                EmployeeSalarySettings::create([
                    'position_id' => $position->id,
                    'base_salary' => $baseSalary,
                    'rate_per_hour' => round($ratePerHour, 2),
                    'rate_per_day' => round($ratePerDay, 2),
                    'status' => 1,
                ]);

                return $position;
            });

            return response()->json(['message' => 'New position "' . $newPosition->name . '" created successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}

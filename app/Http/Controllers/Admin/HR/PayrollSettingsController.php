<?php

namespace App\Http\Controllers\Admin\HR;

use App\Models\Employee;
use App\Models\Position;
use App\Models\PayrollSettings;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EmployeeSalarySettings;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdateSalarySettingRequest;
use App\Http\Requests\UpdatePayrollSettingsRequest;

class PayrollSettingsController extends Controller
{
    public function updatePayrollSettings(UpdatePayrollSettingsRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $validatedData = $request->validated();

                $settings = PayrollSettings::firstOrCreate(['id' => 1]);
                $settings->update($validatedData);

                Employee::query()->update([
                    'sss' => $validatedData['sss'],
                    'philhealth' => $validatedData['philhealth'],
                    'pagibig' => $validatedData['pagibig'],
                ]);
            });

            return response()->json(['message' => 'Contribution settings and all employees have been updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
        try {
            $validatedData = $request->validated();
            $baseSalary = $validatedData['base_salary'];

            $ratePerDay = 0;
            $ratePerHour = 0;
            if ($baseSalary > 0 && self::WORKING_DAYS_PER_MONTH > 0 && self::WORKING_HOURS_PER_DAY > 0) {
                $ratePerDay = $baseSalary / self::WORKING_DAYS_PER_MONTH;
                $ratePerHour = $ratePerDay / self::WORKING_HOURS_PER_DAY;
            }

            $salarySetting = EmployeeSalarySettings::findOrFail($id);

            DB::transaction(function () use ($salarySetting, $baseSalary, $ratePerHour, $ratePerDay) {
                $salarySetting->update([
                    'base_salary' => $baseSalary,
                    'rate_per_hour' => round($ratePerHour, 2),
                    'rate_per_day' => round($ratePerDay, 2),
                ]);

                if ($salarySetting->position_id) {
                    Employee::where('position_id', $salarySetting->position_id)
                        ->update(['base_salary' => $baseSalary]);
                }
            });

            return response()->json(['message' => 'Salary setting and affected employees have been updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function storePositionAndSalary(StorePositionRequest $request)
    {
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

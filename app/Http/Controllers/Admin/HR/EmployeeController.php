<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Enums\Level;
use App\Models\User;
use App\Models\Status;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Schedule;
use App\Models\Department;
use App\Helpers\MailSender;
use Illuminate\Http\Request;
use App\Models\PayrollSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\EmployeeSalarySettings;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Controllers\GenerateIdController;
use Illuminate\Validation\ValidationException;


class EmployeeController extends Controller
{
    public function manage()
    {
        
        $departments = Department::whereNot('name', 'Administration')->whereNot('name', 'Executives')->get();
        $data = ['address' => '', 'first_name' => '', 'middle_name' => '', 'last_name' => '', 'email' => '', 'phone_number' => '', 'postal_code' => '', 'gender' => '', 'birth_date' => '', 'age' => '', 'citizenship' => '', 'department' => '', 'position' => '', 'position_id' => '', 'level' => '', 'supervisor' => '', 'supervisor_id' => '', 'sss' => '', 'pagibig' => '', 'philhealth' => '', 'salary' => '',];
        $mode = 'add';
        $title = 'Add';
        $id = null;
        return view('pages.admin.human_resources.manage-employee', compact('departments', 'data', 'mode', 'title', 'id'));
    }

    public function getSupervisorForPosition(Request $request)
    {
        $position_id = $request->input('position');
        $position = Position::find($position_id);

        if (!$position) {
            return response()->json([], 404);
        }

        if ($position->level === 'manager') {
            $supervisorIds = Employee::where('level', 'ceo')->pluck('id');
        } else {
            $levels = ['staff', 'supervisor', 'manager', 'ceo'];
            $currentLevelIndex = array_search($position->level, $levels);
            $higherLevels = array_slice($levels, $currentLevelIndex + 1);

            $supervisorIds = Employee::whereHas('position', function ($query) use ($position, $higherLevels) {
                $query->where('department_id', $position->department_id)
                    ->whereIn('level', $higherLevels);
            })->pluck('id');

            if ($supervisorIds->isEmpty()) {
                $supervisorIds = Employee::where('level', 'manager')->where('department', 2)->pluck('id');
            }

            if ($supervisorIds->isEmpty()) {
                $supervisorIds = Employee::where('level', 'ceo')->pluck('id');
            }
        }

        $supervisors = User::whereIn('id', $supervisorIds)
            ->get(['id', 'first_name', 'last_name']);

        return response()->json($supervisors);
    }


    public function getPositions(Request $request)
    {
        $department = $request->input('department');

        if (!$department) {
            return response()->json(['error' => 'Missing department'], 400);
        }
        $positions = Position::inDepartment($department)->get();

        $positions = Position::where('department_id', $department)
            ->select('id', 'name', 'level')
            ->get();

        return response()->json($positions);
    }

    private function fetchCEO()
    {
        $ceo = Employee::where('position', 'LIKE', '%ceo%')->get(['id']);
        $id = $ceo->pluck('id');
        return $ceo = User::whereIn('id', $id)
            ->get(['id', 'first_name', 'last_name']);
    }
    public function getCEO()
    {
        return response()->json($this->fetchCEO());
    }

    public function getPayrollSettings()
    {
        $settings = PayrollSettings::where('status', 1)->first();

        if (!$settings) {
            return response()->json([
                'sss' => '0.00',
                'philhealth' => '0.00',
                'pagibig' => '0.00'
            ], 404); // Not Found
        }

        return response()->json($settings);
    }

    public function getSalaryByPosition(Request $request)
    {
        $request->validate([
            'position_id' => 'required|integer|exists:positions,id'
        ]);

        $salary = EmployeeSalarySettings::where('position_id', $request->position_id)
            ->where('status', 1)
            ->first();

        if (!$salary) {
            return response()->json(null);
        }

        return response()->json($salary);
    }

    public function getSalarySettings()
    {
        try {
            $salaries = EmployeeSalarySettings::with(['position.department'])
                ->where('status', 1)
                ->orderBy('updated_at', 'desc')->get();

            return response()->json([
                'data' => $salaries->map(function ($setting) {
                    return [
                        'id'            => $setting->id,
                        'position_id'   => $setting->position_id,
                        'position'      => optional($setting->position)->name,
                        'department'    => optional($setting->position)->department->name,
                        'base_salary'   => $setting->base_salary,
                        'rate_per_hour' => $setting->rate_per_hour,
                        'rate_per_day'  => $setting->rate_per_day,
                        'status'        => Status::getStatusText($setting->status),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch salary settings list: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function storeEmployee(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $levelEnum = Level::tryFrom(strtolower(trim($validated['level'] ?? '')));
            if (!$levelEnum) {
                 throw ValidationException::withMessages(['level' => 'Invalid role specified.']);
            }

            $employee_Id = GenerateIdController::generateID('employee');

            $firstName = $validated['first_name'];
            $lastName = $validated['last_name'];
            $password = bcrypt($lastName . $firstName);

            $user = User::create([
                'id' => $employee_Id,
                'first_name' => $firstName,
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'password' => $password,
                'phone_number' => $validated['phone_number'],
                'user_type' => 'employee',
                'must_change_password' => true,
            ]);

            $employee = Employee::create([
                'id' => $employee_Id,
                'user_id' => $employee_Id,
                'address' => $validated['address'],
                'postal_code' => $validated['postal_code'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'age' => Carbon::parse($validated['birth_date'])->age,
                'phone_number' => $validated['phone_number'],
                'citizenship' => $validated['citizenship'],
                'department' => $validated['department'],
                'level' => $levelEnum,
                'position_id' => $validated['position_id'],
                'supervisor_id' => $validated['supervisor_id'],
                'sss' => $validated['sss'] ?? 600.00,
                'pagibig' => $validated['pagibig'] ?? 100.00,
                'philhealth' => $validated['philhealth'] ?? 450.00,
                'base_salary' => $validated['base_salary'],
                'status' => 1
            ]);

            if (!empty($validated['days_of_week']) && !empty($validated['time_in']) && !empty($validated['time_out'])) {
                Schedule::create([
                    'employee_id' => $employee->id,
                    'title' => $validated['schedule_title'] ?? null,
                    'days_of_week' => $validated['days_of_week'],
                    'time_in' => $validated['time_in'],
                    'time_out' => $validated['time_out'],
                    'color' => $validated['color'] ?? null,
                    'description' => $validated['description'] ?? null,
                ]);
            }
            // --- END ADDED ---

            DB::commit();

            // --- Send Email ---
            try {
                $content = [
                    'email' => $validated['email'],
                    'title' => 'Welcome aboard to Tinatangi Cafe!',
                    'name' => $firstName . ' ' . $lastName,
                    'password' => $lastName . $firstName,
                    'blade_file' => 'emails.new-employee',
                    'login_link' => url('/login'),
                ];
                MailSender::sendEmployeeEmail($content);
            } catch (\Exception $mailError) {

            }

            return response()->json(['message' => 'Employee and schedule added successfully!'], 201); // Updated message

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateEmployee(UpdateEmployeeRequest $request, $employee_id)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $employee = Employee::findOrFail($employee_id);
            $user = $employee->user;

            $user->update([
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'phone_number' => $validated['phone_number'],
            ]);

            $levelEnum = Level::tryFrom(strtolower(trim($validated['level'] ?? '')));
             if (!$levelEnum) {
                 throw ValidationException::withMessages(['level' => 'Invalid role specified.']);
             }
            $employee->update([
                'address' => $validated['address'],
                'postal_code' => $validated['postal_code'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'age' => Carbon::parse($validated['birth_date'])->age,
                'phone_number' => $validated['phone_number'],
                'citizenship' => $validated['citizenship'],
                'department' => $validated['department'],
                'level' => $levelEnum,
                'position_id' => $validated['position_id'],
                'supervisor_id' => $validated['supervisor_id'],
                'base_salary' => $validated['base_salary'],
            ]);


             if (!empty($validated['days_of_week']) && !empty($validated['time_in']) && !empty($validated['time_out'])) {
                $employee->schedule()->updateOrCreate(
                    ['employee_id' => $employee->id],
                    [
                        'title' => $validated['schedule_title'] ?? null,
                        'days_of_week' => $validated['days_of_week'],
                        'time_in' => $validated['time_in'],
                        'time_out' => $validated['time_out'],
                        'color' => $validated['color'] ?? null,
                        'description' => $validated['description'] ?? null,
                    ]
                );
            } else {
                $employee->schedule()->delete();
            }


            DB::commit();
            return response()->json(['message' => 'Employee and schedule updated successfully!'], 200);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // {
    //     try {
    //         DB::beginTransaction();

    //         $validated = $request->validated();

    //         $levelInput = strtolower(trim($validated['level']));
    //         $levelEnum = Level::tryFrom($levelInput);

    //         $employee_Id = GenerateIdController::generateID('employee');

    //         // Create accounts
    //         $user = User::create([
    //             'id' => $employee_Id,
    //             'first_name' => $validated['first_name'],
    //             'middle_name' => $validated['middle_name'] ?? null,
    //             'last_name' => $validated['last_name'],
    //             'email' => $validated['email'],
    //             'password' => bcrypt(Sanitizer::clean(Sanitizer::clean($_POST["last_name"]) . Sanitizer::clean($_POST["first_name"]))),
    //             'phone_number' => $validated['phone_number'],
    //             'user_type' => 'employee',
    //         ]);
    //         $user->save();
    //         Employee::create([
    //             'id' => User::where('email', $validated['email'])->first()->id,
    //             'user_id' => User::where('email', $validated['email'])->first()->id,
    //             'address' => $validated['address'],
    //             'postal_code' => $validated['postal_code'],
    //             'gender' => $validated['gender'],
    //             'birth_date' => $validated['birth_date'],
    //             'age' => $validated['age'],
    //             'phone_number' => $validated['phone_number'],
    //             'citizenship' => $validated['citizenship'],
    //             'department' => $validated['department'],
    //             'level' => $levelEnum,
    //             'position_id' => $validated['position_id'],
    //             'supervisor_id' => $validated['supervisor_id'],
    //             'sss' => 600,
    //             'pagibig' => 100,
    //             'philhealth' => 450,
    //             'base_salary' => $validated['base_salary'],
    //         ]);
    //         DB::commit();
    //         // Send email with login details
    //         $content = [
    //             'email' => $validated['email'],
    //             'title' => 'Welcome aboard to Tinatangi Cafe!',
    //             'name' => $validated['first_name'] . ' ' . $validated['last_name'],
    //             'password' => Sanitizer::clean(Sanitizer::clean($_POST["last_name"]) . Sanitizer::clean($_POST["first_name"])),
    //             'blade_file' => 'emails.new-employee',
    //             'login_link' => url('/login'),
    //         ];

    //         MailSender::sendEmployeeEmail($content);

    //         return response()->json(['message' => 'Employee added successfully!'], 201);
    //     } catch (ValidationException $e) {
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
    //     }
    // }

    public function editEmployee($id)
    {
        $title = 'Edit';

        $employee = Employee::with('schedule')->findOrFail($id);

        $user = User::findOrFail($id);
        $departments = Department::all();

        $positionName = $employee->position->name;

        $mode = 'edit';
        $direct_supervisor = User::find($employee->supervisor_id);
        $direct_supervisor_name = $direct_supervisor->first_name . ' ' . $direct_supervisor->last_name;

        $data = [
            'address' => $employee->address,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'postal_code' => $employee->postal_code,
            'gender' => $employee->gender,
            'birth_date' => $employee->birth_date,
            'age' => $employee->age,
            'citizenship' => $employee->citizenship,
            'department' => $employee->department,
            'level' => $employee->level,
            'position' => $positionName,
            'position_id' => $employee->position_id,
            'supervisor' => $direct_supervisor_name,
            'supervisor_id' => $employee->supervisor_id,
            'sss' => $employee->sss,
            'pagibig' => $employee->pagibig,
            'philhealth' => $employee->philhealth,
            'base_salary' => $employee->base_salary,

            'schedule' => $employee->schedule,
        ];

        return view("pages.admin.human_resources.manage-employee", compact("data", "departments", "mode", "title", "id"));
    }

}

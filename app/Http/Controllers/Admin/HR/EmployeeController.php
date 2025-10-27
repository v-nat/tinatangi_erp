<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Enums\Level;
use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Schedule;
use App\Models\Department;
use App\Helpers\MailSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Controllers\GenerateIdController;
use Illuminate\Validation\ValidationException;


class EmployeeController extends Controller
{
    public function manage()
    {
        $departments = Department::whereNot('name', 'Administrator')->get();
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

        // If the level is 'manager', return CEO(s) directly
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


    public function storeEmployee(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Attempt to resolve Level Enum, handle potential failure
            $levelEnum = Level::tryFrom(strtolower(trim($validated['level'] ?? '')));
            if (!$levelEnum) {
                 // Throw validation exception or handle default/error case
                 throw ValidationException::withMessages(['level' => 'Invalid role specified.']);
            }

            // Generate Employee ID
            $employee_Id = GenerateIdController::generateID('employee');

            // --- Create User ---
            // Sanitize names right before use if absolutely necessary
            $firstName = $validated['first_name']; // Already validated as string
            $lastName = $validated['last_name'];
            $password = bcrypt($lastName . $firstName); // Simpler password generation

            $user = User::create([
                'id' => $employee_Id,
                'first_name' => $firstName,
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'password' => $password, // Use the variable
                'phone_number' => $validated['phone_number'],
                'user_type' => 'employee',
            ]);
            // No need to call $user->save() after create()

            // --- Create Employee ---
            $employee = Employee::create([
                'id' => $employee_Id, // Use the generated ID
                'user_id' => $employee_Id, // Use the generated ID
                'address' => $validated['address'],
                'postal_code' => $validated['postal_code'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                // Calculate age server-side for accuracy
                'age' => Carbon::parse($validated['birth_date'])->age,
                'phone_number' => $validated['phone_number'],
                'citizenship' => $validated['citizenship'],
                'department' => $validated['department'], // Assuming 'department' is the ID
                'level' => $levelEnum,
                'position_id' => $validated['position_id'],
                'supervisor_id' => $validated['supervisor_id'],
                'sss' => $validated['sss'] ?? 600.00, // Use validated or default
                'pagibig' => $validated['pagibig'] ?? 100.00,
                'philhealth' => $validated['philhealth'] ?? 450.00,
                'base_salary' => $validated['base_salary'],
                'status' => 1 // Assuming 1 means active
            ]);

            // --- ADDED: Create Schedule IF data is present ---
            if (!empty($validated['days_of_week']) && !empty($validated['time_in']) && !empty($validated['time_out'])) {
                Schedule::create([
                    'employee_id' => $employee->id, // Link to the created employee
                    'title' => $validated['schedule_title'] ?? null, // Use validated field name
                    'days_of_week' => $validated['days_of_week'], // Already an array from request/casting
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

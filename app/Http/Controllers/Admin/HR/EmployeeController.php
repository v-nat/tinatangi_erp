<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Enums\Level;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\Sanitizer;
use App\Helpers\MailSender;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;


class EmployeeController extends Controller
{
    public function manage()
    {
        $departments = Department::all();
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

            // Fallbacks
            if ($supervisorIds->isEmpty()) {
                $supervisorIds = Employee::where('level', 'manager')->where('department', 2)->pluck('id');
            }

            if ($supervisorIds->isEmpty()) {
                $supervisorIds = Employee::where('level', 'ceo')->pluck('id');
            }
        }
        // $department = Department::findOrFail(Employee::findOrFail($supervisorIds)->pluck('department'))->pluck('name');
        $supervisors = User::whereIn('id', $supervisorIds)
            ->get(['id', 'first_name', 'last_name']);
        // $nameWithDept = $supervisors . '( ' . $department . ' )';
        return response()->json($supervisors);
    }


    public function getPositions(Request $request)
    {
        $department = $request->input('department');
        // dd  ($department);
        if (!$department) {
            return response()->json(['error' => 'Missing department'], 400);
        }
        $positions = Position::inDepartment($department)->get();
        // $positions = $positions->map(function ($position) {
        //     return ['id'=> $position->id,'name'=> $position->name];
        // });
        $positions = Position::where('department_id', $department)
            ->select('id', 'name', 'level')
            ->get();
        // dd ($positions);

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

            $levelInput = strtolower(trim($validated['level']));
            $levelEnum = Level::tryFrom($levelInput);

            $year = Carbon::now()->format('Y');
            do {
                $random = rand(10000, 99999);
                $employee_Id = $year . $random;
            } while (User::pluck('id')->contains($employee_Id));

            // Create accounts
            $user = User::create([
                'id' => $employee_Id,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => bcrypt(Sanitizer::clean($_POST["last_name"]) . $_POST["first_name"]),
                'phone_number' => $validated['phone_number'],
                'user_type' => 'employee',
            ]);
            DB::commit();
            DB::beginTransaction();
            Employee::create([
                'id' => User::where('email', $validated['email'])->first()->id,
                'user_id' => User::where('email', $validated['email'])->first()->id,
                'address' => $validated['address'],
                'postal_code' => $validated['postal_code'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'age' => $validated['age'],
                'phone_number' => $validated['phone_number'],
                'citizenship' => $validated['citizenship'],
                'department' => $validated['department'],
                'level' => $levelEnum,
                'position_id' => $validated['position_id'],
                'supervisor_id' => $validated['supervisor_id'],
                'sss' => 600,
                'pagibig' => 100,
                'philhealth' => 450,
                'base_salary' => $validated['base_salary'],
            ]);
            DB::commit();
            // Send email with login details
            $content = [
                'email' => $validated['email'],
                'title' => 'Welcome aboard to Tinatangi Cafe!',
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'password' => Sanitizer::clean($_POST["last_name"] . $_POST["first_name"]),
                'blade_file' => 'emails.new-employee',
                'login_link' => url('/login'),
            ];

            MailSender::sendEmployeeEmail($content);

            return response()->json(['message' => 'Employee added successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function editEmployee($id)
    {
        $title = 'Edit';
        $employee = Employee::findOrFail($id);
        $user = User::findOrFail($id);
        $departments = Department::all();


        $positionName = $employee->position->name;
        // dd($positionInfo);
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
        ];
        // dd  ($employee);
        return view("pages.admin.human_resources.manage-employee", compact("data", "departments", "mode", "title", "id"));
    }

    public function updateEmployee(UpdateEmployeeRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $levelInput = strtolower(trim($validated['level']));
            $levelEnum = Level::tryFrom($levelInput);

            $user = User::find($id);
            $employee = Employee::find($id);
            $user->first_name = $validated['first_name'];
            $user->middle_name = $validated['middle_name'];
            $user->last_name = $validated['last_name'];
            $user->phone_number = $validated['phone_number'];
            $employee->birth_date = $validated['birth_date'];
            $employee->age = $validated['age'];
            $employee->gender = $validated['gender'];
            $employee->address = $validated['address'];
            $employee->postal_code = $validated['postal_code'];
            $employee->citizenship = $validated['citizenship'];
            $employee->phone_number = $validated['phone_number'];
            $employee->department = $validated['department'];
            $employee->level = $levelEnum;
            $employee->position_id = $validated['position_id'];
            $employee->supervisor_id = $validated['supervisor_id'];
            $employee->base_salary = $validated['base_salary'];
            $user->save();
            $employee->save();

            DB::commit();

            return response()->json(['message' => 'Employee updated successfully!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

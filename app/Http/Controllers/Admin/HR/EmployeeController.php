<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\Sanitizer;
use App\Helpers\MailSender;

class EmployeeController extends Controller
{
    public function manage()
    {
        $departments = Department::all();
        $supervisors = Employee::where('position', 'LIKE', '%supervisor%')
            ->whereNull('deleted_at') // if using soft deletes
            ->get();
        return view('pages.admin.human_resources.manage-employee', compact('departments'));
    }

    public function getSupervisors(Request $request)
    {
        $department = $request->input('department');
        if (!$department) {
            return response()->json(['error' => 'Missing department'], 400);
        }

        $supervisors = Employee::where('department', $department)
            ->where('position', 'LIKE', '%supervisor%')
            ->get(['id']);

        $supervisors = User::whereIn('id', $supervisors->pluck('id'))
            ->get(['id', 'first_name', 'last_name']);

        return response()->json($supervisors);
    }
    public function getCEO()
    {
        $ceo = Employee::where('position', 'LIKE', '%ceo%')->get(['id']);

        $id = $ceo->pluck('id');
        $ceo = User::whereIn('id', $id)
            ->get(['id', 'first_name', 'last_name']);

        return response()->json($ceo);
    }
    public function storeEmployee(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:10',
                'gender' => 'required|string',
                'birth_date' => 'required|date',
                'age' => 'required|integer|min:18',
                'phone_number' => [ 'required', 'string', 'max:13', 'regex:/^(09|\+639)\d{9}$/'],
                'citizenship' => 'required|string|max:50',
                'department' => 'required|integer',
                'position' => 'required',
                'direct_supervisor' => 'required',
                'sss' => 'required|integer',
                'pagibig' => 'required|integer',
                'philhealth' => 'required|integer',
                'salary' => 'required|numeric|min:0',
            ]);

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
                'position' => $validated['position'],
                'direct_supervisor' => $validated['direct_supervisor'],
                'sss' => $validated['sss'],
                'pagibig' => $validated['pagibig'],
                'philhealth' => $validated['philhealth'],
                'salary' => $validated['salary'],
            ]);
            // Send email with login details
            $content = [
                'email' => $validated['email'],
                'title' => 'Welcome aboard to Tinatangi Cafe!',
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'password' => Sanitizer::clean( $_POST["last_name"] . $_POST["first_name"]),
                'blade_file' => 'emails.new-employee',
                'login_link' => url('/login'),
            ];

            MailSender::sendEmployeeEmail($content);

            DB::commit();

            return response()->json(['message' => 'Employee added successfully!'], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

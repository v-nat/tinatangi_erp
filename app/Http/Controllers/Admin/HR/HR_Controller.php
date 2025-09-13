<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class HR_Controller extends Controller
{
    //
    public function index()
    {
        return view('pages.admin.human_resources.dashboard');
    }

    public function employees()
    {
        return view('pages.admin.human_resources.employees');
    }
    public function getEmployees()
    {
        try {
            $employees = Employee::with(['user', 'departmentRS', 'user.statusRS', 'directSupervisorRS.user'])->orderBy('created_at', 'desc')->get();
            // dd($employees);
            return response()->json([
                'data' => $employees->map(function ($e) {
                    return [
                        'employee_id'       => $e->id,
                        'name'              => $e->user->full_name ?? 'N/A',
                        'position'          => $e->position ?? 'N/A',
                        'department'        => $e->departmentRS->name ?? 'N/A',
                        'email'             => $e->user->email ?? 'N/A',
                        'direct_supervisor' => optional(optional($e->directSupervisorRS)->user)->full_name ?? 'Unassigned',
                        'status'            => $e->user->statusRS->status ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function otApp()
    {
        return view('pages.admin.human_resources.ot-app');
    }
    public function leaveApp()
    {
        return view('pages.admin.human_resources.leave-app');
    }
}

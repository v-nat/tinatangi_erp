<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    //
    public function index()
    {
        try {
            $query = Overtime::with(['statusRS', 'employeeRS', 'employeeRS.deptRS'])->orderBy('date', 'desc');
            // dd($query);
            $overtimes = $query->get();
            $result = $overtimes->map(function ($ot) {
                return [
                    // 'dept'=> $ot->employeeRS->department,
                    'overtime_id'       => $ot->id,
                    'employee'          => optional(optional($ot->employeeRS)->userRS)->full_name,
                    'department'        => optional(optional($ot->employeeRS)->deptRS)->name,
                    'position'          => $ot->employeeRS->position ?? 'N/A',
                    'date'              => $ot->date ?? 'N/A',
                    'time_start'        => $ot->time_start ?? 'N/A',
                    'time_end'          => $ot->time_end ?? 'N/A',
                    'reason'            => $ot->reason ?? 'N/A',
                    'status'            => Status::getStatusText($ot->status),
                ];
            });
            // dd($result);
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function approve($id)
    {
        try {
            $overtime = Overtime::findOrFail($id);
            $employee = Auth::user()->id;

            if (!$employee) {
                throw new \Exception("Employee data not found for this user");
            }

            $overtime->update([
                'status' => 13, // Approved    
                'reason' => request()->input('reason', ''),
                'approved_by' => $employee->id, // Use employee_id instead of user id
                'approval_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Overtime request approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve overtime request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        try {
            request()->validate(['reason' => 'required|string']);

            $overtime = Overtime::findOrFail($id);
            $employee = Auth::user()->id; // Get the employee data for the logged in user

            if (!$employee) {
                throw new \Exception("Employee data not found for this user");
            }

            $overtime->update([
                'status' => 12, // Rejected
                'reason' => request()->input('reason'),
                'approved_by' => $employee->id, // Use employee_id instead of user id
                'approval_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Overtime request rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject overtime request: ' . $e->getMessage()
            ], 500);
        }
    }
}

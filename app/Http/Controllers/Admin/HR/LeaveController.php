<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\Leave;
use App\Models\Status;

class LeaveController extends Controller
{
    //
    public function index()
    {
        try {
            $query = Leave::with(['statusRS', 'employeeRS', 'employeeRS.deptRS' ])->orderBy('start_date', 'desc');
            // dd($query);
            $leaves = $query->get();
            $result = $leaves->map(function ($leave) {
                return [
                    // 'dept'=> $leave->employeeRS->department,
                    'leave_id'       => $leave->id,
                    'employee'          => optional(optional($leave->employeeRS)->userRS)->full_name,
                    'department'        => optional(optional($leave->employeeRS)->deptRS)->name,
                    'position'          => $leave->employeeRS->position ?? 'N/A',
                    'start_date'         => $leave->start_date ?? 'N/A',
                    'end_date'          => $leave->end_date ?? 'N/A',
                    'reason'            => $leave->reason ?? 'N/A',
                    'status' =>         Status::getStatusText($leave->status),
                ];
            });
            // dd($result);
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function approve($leave_id)
    {
        try {
            $leave = Leave::findOrFail($leave_id);
            // $employee = Auth::user()->id;

            $leave->status = 13;
            $leave->reason = request()->input('reason', '');
            $leave->approved_by = auth('')->user()->id;
            $leave->approval_date = now();
            $leave->save();
            return response()->json([
                'success' => true,
                'message' => 'Leave request approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject($leave_id)
    {
        try {
            $leave = Leave::findOrFail($leave_id);
            // $employee = Auth::user()->id;

            $leave->status = 12;
            $leave->reason = request()->input('reason', '');
            $leave->approved_by = auth('')->user()->id;
            $leave->approval_date = now();
            $leave->save();
            return response()->json([
                'success' => true,
                'message' => 'Leave request rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserReq($id)
    {
        try {
            $query = Leave::where('employee_id', $id);

            $leaves = $query->get();
            // dd($query);
            $result = $leaves->map(function ($leave) {
                return [
                    'start_date'        => $leave->start_date ?? 'N/A',
                    'end_date'          => $leave->end_date ?? 'N/A',
                    'reason'            => $leave->reason ?? 'N/A',
                    'status'            => Status::getStatusText($leave->status),
                ];
            });
            // dd($result);
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}

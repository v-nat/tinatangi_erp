<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
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
}

<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateOvertimeRequest;

class OvertimeController extends Controller
{
    //
    public function index()
    {
        try {
            $query = Overtime::with(['statusRS', 'employeeRS', 'employeeRS.deptRS'])->orderBy('updated_at', 'desc');
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

    public function approve($overtime_id)
    {
        try {
            $overtime = Overtime::findOrFail($overtime_id);
            // $employee = Auth::user()->id;

            $overtime->status = 13;
            $overtime->reason = request()->input('reason', '');
            $overtime->approved_by = auth('')->user()->id;
            $overtime->approval_date = now();
            $overtime->save();
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

    public function reject($overtime_id)
    {
        try {
            $overtime = Overtime::findOrFail($overtime_id);
            // $employee = Auth::user()->id;

            $overtime->status = 12;
            $overtime->reason = request()->input('reason', '');
            $overtime->approved_by = auth('')->user()->id;
            $overtime->approval_date = now();
            $overtime->save();
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

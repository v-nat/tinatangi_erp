<?php

namespace App\Http\Controllers\Admin\HR;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Overtime;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Requests\StoreOvertimeRequest;
use Illuminate\Validation\ValidationException;


class OvertimeController extends Controller
{
    public function index()
    {
        try {
            $query = Overtime::with(['statusRS', 'employeeRS', 'employeeRS.deptRS'])->orderBy('updated_at', 'desc');

            $overtimes = $query->get();
            $result = $overtimes->map(function ($ot) {
                return [
                    'overtime_id'       => $ot->id,
                    'employee'          => optional(optional($ot->employeeRS)->userRS)->full_name,
                    'department'        => optional(optional($ot->employeeRS)->deptRS)->name,
                    'position'          => optional(optional($ot->employeeRS)->position)->name ?? 'N/A',
                    'date'              => $ot->date ?? 'N/A',
                    'time_start'        => $ot->time_start ?? 'N/A',
                    'time_end'          => $ot->time_end ?? 'N/A',
                    'reason'            => $ot->reason ?? 'N/A',
                    'status'            => Status::getStatusText($ot->status),
                ];
            });

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function approve($overtime_id)
    {
        try {
            $overtime = Overtime::findOrFail($overtime_id);

            if (auth('')->user()->id == $overtime->employee_id || !AuthController::checkAccess()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }

            $attendance = Attendance::where('date', $overtime->date)
                ->where('employee_id', $overtime->employee_id)->first();
            $start = Carbon::parse($overtime->time_start);
            $end = Carbon::parse($overtime->time_end);
            $minutes = $start->diffInMinutes($end);
            $attendance->overtime_minutes = $minutes;

            $attendance->save();
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

            if (auth('')->user()->id == $overtime->employee_id || !AuthController::checkAccess()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }

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

    public function getUserReq($id)
    {
        try {
            $query = Overtime::where('employee_id', $id);

            $overtimes = $query->get();

            $result = $overtimes->map(function ($ot) {
                return [
                    'date'              => $ot->date ?? 'N/A',
                    'time_start'        => $ot->time_start ?? 'N/A',
                    'time_end'          => $ot->time_end ?? 'N/A',
                    'total_minutes'     => $ot->total_minutes ?? '',
                    'reason'            => $ot->reason ?? 'N/A',
                    'status'            => Status::getStatusText($ot->status),
                ];
            });

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function submitReq(StoreOvertimeRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            $start = Carbon::parse($validated['time_start']);
            $end = Carbon::parse($validated['time_end']);

            $total_minutes = $start->diffInMinutes($end);

            Overtime::create([
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
                'time_start' => $validated['time_start'],
                'time_end' => $validated['time_end'],
                'total_minutes' => $total_minutes,
                'reason' => $validated['reason'] ?? '',
                'status' => 11,
            ]);
            DB::commit();

            return response()->json(['message' => 'Overtime submitted successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Controller Error: ' . $e->getMessage()], 500);
        }
    }
}

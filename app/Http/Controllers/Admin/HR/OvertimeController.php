<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOvertimeRequest;
use App\Models\Overtime;
use App\Models\Attendance;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


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
                    'position'          => optional(optional($ot->employeeRS)->position)->name ?? 'N/A',
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

    public function getUserReq($id)
    {
        try {
            $query = Overtime::where('employee_id', $id);

            $overtimes = $query->get();
            // dd($query);
            $result = $overtimes->map(function ($ot) {
                return [
                    'date'              => $ot->date ?? 'N/A',
                    'time_start'        => $ot->time_start ?? 'N/A',
                    'time_end'          => $ot->time_end ?? 'N/A',
                    'total_minutes'     => $ot->total_minutes ??'',
                    'reason'            => $ot->reason ?? 'N/A',
                    'status'            => Status::getStatusText($ot->status),
                ];
            });
            // dd($result);
            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function submitReq(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|integer|exists:employees,id',
                'date'=> 'required|unique:overtimes,employee_id,date',
                'time_start' => 'required|date_format:H:i',
                'time_end' => 'required|date_format:H:i|after:time_start',
                'reason'=> 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()->all()
                ], 422);
            }


            $start = Carbon::parse($request->time_start);
            $end = Carbon::parse($request->time_end);

            $total_minutes = $start->diffInMinutes($end);
            // dd($request);
            Overtime::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'time_start' => $request->time_start,
                'time_end' => $request->time_end,
                'total_minutes' => $total_minutes,
                'reason' => $request->reason ?? '',
                'status' => 7,
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

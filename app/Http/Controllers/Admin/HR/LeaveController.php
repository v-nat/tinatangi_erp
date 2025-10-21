<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\AuthController;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreLeaveRequest;
use Illuminate\Validation\ValidationException;

class LeaveController extends Controller
{
    public function index()
    {
        try {
            $query = Leave::with(['statusRS', 'employeeRS', 'employeeRS.deptRS'])->orderBy('start_date', 'desc');

            $leaves = $query->get();
            $result = $leaves->map(function ($leave) {
                return [
                    'leave_id'       => $leave->id,
                    'employee'          => optional(optional($leave->employeeRS)->userRS)->full_name,
                    'department'        => optional(optional($leave->employeeRS)->deptRS)->name,
                    'position'          => optional(optional($leave->employeeRS)->position)->name ?? 'N/A',
                    'start_date'         => $leave->start_date ?? 'N/A',
                    'end_date'          => $leave->end_date ?? 'N/A',
                    'reason'            => $leave->reason ?? 'N/A',
                    'status'            => Status::getStatusText($leave->status),
                ];
            });

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function approve($leave_id)
    {
        try {
            $leave = Leave::findOrFail($leave_id);

            if (auth('')->user()->id == $leave->employee_id || !AuthController::checkAccess()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }

            $reason = request()->input('reason', '');

            $leave->status = 13;
            if ($reason) {
                $leave->reason = $reason;
            }
            $leave->approved_by = auth('')->user()->id;
            $leave->approval_date = now();
            $leave->save();

            $startDate = Carbon::create($leave->start_date);
            $endDate = Carbon::create($leave->end_date);

            $records = [];

            for ($i = 0; $startDate->copy()->addDays($i)->lte($endDate); $i++) {
                $date = $startDate->copy()->addDays($i)->toDateString();

                $records[] = [
                    'employee_id'        => $leave->employee_id,
                    'date'               => $date,
                    'time_in'            => null,
                    'time_out'           => null,
                    'hours_worked'       => 480,
                    'tardiness'          => 0,
                    'is_leave'           => true,
                    'tardiness_minutes'  => 0,
                    'leave_id'           => $leave_id,
                    'overtime_minutes'   => 0,
                    'overtime_id'        => null,
                    'status'             => 8,
                    'created_at'         => Carbon::now(),
                    'updated_at'         => Carbon::now(),
                ];
            }

            DB::table('attendances')->insert($records);

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

            if (auth('')->user()->id == $leave->employee_id && !AuthController::checkAccess()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }

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
            $result = $leaves->map(function ($leave) {
                return [
                    'start_date'        => $leave->start_date ?? 'N/A',
                    'end_date'          => $leave->end_date ?? 'N/A',
                    'reason'            => $leave->reason ?? 'N/A',
                    'status'            => Status::getStatusText($leave->status),
                ];
            });

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function submitReq(StoreLeaveRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            Leave::create([
                'employee_id' => $validated['employee_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'reason' => $validated['reason'] ?? '',
                'status' => 11,
            ]);
            DB::commit();

            return response()->json(['message' => 'Leave submitted successfully!'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Controller Error: ' . $e->getMessage()], 500);
        }
    }
}

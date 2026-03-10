<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\AuthController;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Status;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreLeaveRequest;
use Illuminate\Validation\ValidationException;

class LeaveController extends Controller
{
    private function determineIsPaid(string $reason): bool
    {
        return !str_starts_with($reason, 'Other');
    }
    private function countWorkingDays($start, $end): int
    {
        return collect(CarbonPeriod::create($start, $end))
            ->filter(fn ($d) => $d->isWeekday())
            ->count();
    }

    public function index()
    {
        try {
            $query = Leave::with(['statusRS', 'employeeRS', 'employeeRS.deptRS'])->orderBy('start_date', 'desc');

            $leaves = $query->get();
            $result = $leaves->map(function ($leave) {
                return [
                    'leave_id'       => $leave->id,
                    'employee'       => optional(optional($leave->employeeRS)->userRS)->full_name,
                    'department'     => optional(optional($leave->employeeRS)->deptRS)->name,
                    'position'       => optional(optional($leave->employeeRS)->position)->name ?? 'N/A',
                    'start_date'     => $leave->start_date ?? 'N/A',
                    'end_date'       => $leave->end_date ?? 'N/A',
                    'days_count'     => ($leave->start_date && $leave->end_date)
                                            ? $this->countWorkingDays($leave->start_date, $leave->end_date)
                                            : 0,
                    'reason'         => $leave->reason ?? 'N/A',
                    'is_paid'        => (bool) $leave->is_paid,
                    'attachment_url' => $leave->attachment ? asset('storage/app/public/' . $leave->attachment) : null,
                    'status'         => Status::getStatusText($leave->status),
                    'status_id'      => $leave->status,
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

            if (auth('')->user()->id == $leave->employee_id || !AuthController::checkAuthorization()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }

            $isPaid = request()->has('is_paid')
                ? (bool) request()->input('is_paid')
                : (bool) $leave->is_paid;

            $leave->is_paid       = $isPaid;
            $leave->status        = 13;
            $leave->approved_by   = auth('')->user()->id;
            $leave->approval_date = now();
            $leave->save();

            $startDate = Carbon::create($leave->start_date);
            $endDate   = Carbon::create($leave->end_date);

            Attendance::where('employee_id', $leave->employee_id)
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->forceDelete();

            $employee     = Employee::with('schedule')->findOrFail($leave->employee_id);
            $scheduledDays = $employee->schedule
                ? $employee->schedule->days_of_week
                : [1, 2, 3, 4, 5];

            $records = [];

            for ($i = 0; ($day = $startDate->copy()->addDays($i))->lte($endDate); $i++) {

                if (!in_array($day->dayOfWeek, $scheduledDays)) continue;

                $records[] = [
                    'employee_id'       => $leave->employee_id,
                    'date'              => $day->toDateString(),
                    'time_in'           => null,
                    'time_out'          => null,
                    'hours_worked'      => $isPaid ? 480 : 0,
                    'tardiness'         => 0,
                    'is_leave'          => true,
                    'is_paid_leave'     => $isPaid,
                    'tardiness_minutes' => 0,
                    'leave_id'          => $leave_id,
                    'overtime_minutes'  => 0,
                    'overtime_id'       => null,
                    'status'            => 8,
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
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

            if (auth('')->user()->id == $leave->employee_id && !AuthController::checkAuthorization()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized for this action.'
                ], 401);
            }

            $reason = request()->input('reason', '');

            if (!$reason) {
                return response()->json([
                    'success' => false,
                    'message' => 'A rejection reason is required.'
                ], 422);
            }

            $leave->status        = 12;
            $leave->reason        = $reason;
            $leave->approved_by   = auth('')->user()->id;
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
            $leaves = Leave::where('employee_id', $id)->get();
            $result = $leaves->map(function ($leave) {
                return [
                    'start_date'     => $leave->start_date ?? 'N/A',
                    'end_date'       => $leave->end_date ?? 'N/A',
                    'days_count'     => ($leave->start_date && $leave->end_date)
                                            ? $this->countWorkingDays($leave->start_date, $leave->end_date)
                                            : 0,
                    'reason'         => $leave->reason ?? 'N/A',
                    'is_paid'        => (bool) $leave->is_paid,
                    'attachment_url' => asset('/storage/app/public/' . $leave->attachment) ?? null,
                    'status'         => Status::getStatusText($leave->status),
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

            $today     = Carbon::today();
            $startDate = Carbon::parse($validated['start_date']);
            $endDate   = Carbon::parse($validated['end_date']);

            if ($today->between($startDate, $endDate)) {
                $timedIn = Attendance::where('employee_id', $validated['employee_id'])
                    ->whereDate('date', $today)
                    ->whereNotNull('time_in')
                    ->whereNull('time_out')
                    ->exists();

                if ($timedIn) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'You are currently timed in. Please time out first before filing a leave for today.'
                    ], 422);
                }
            }

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file           = $request->file('attachment');
                $filename       = $file->hashName();
                $path           = 'img/leave_attachments/' . $filename;
                Storage::disk('public')->put($path, $file->get());
                $attachmentPath = $path;
            }

            $isPaid = $this->determineIsPaid($validated['reason']);

            Leave::create([
                'employee_id' => $validated['employee_id'],
                'start_date'  => $validated['start_date'],
                'end_date'    => $validated['end_date'],
                'reason'      => $validated['reason'],
                'attachment'  => $attachmentPath,
                'is_paid'     => $isPaid,
                'status'      => 11,
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

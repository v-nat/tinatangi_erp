<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\BudgetRelease;
use Illuminate\Http\Request;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\Payroll;

class FinanceController extends Controller
{
    //
    public function budgetsIndex()
    {
        return view("pages.admin.finance.budget-releasing");
    }
    public function purchasesIndex()
    {
        return view("pages.admin.finance.purchase-order-approvals");
    }

    public function getPendingRequests()
    {
        try {
            $requests = BudgetRelease::with(['employeeRS', 'statusRS'])
                ->where('status', 11)
                ->orderBy('requested_at', 'desc')->get();
            // dd($employees);
            return response()->json([
                'data' => $requests->map(function ($r) {
                    return [
                        'id'                => $r->id,
                        'release_id'        => $r->release_id,
                        'type'              => $r->type,
                        'amount'            => $r->amount,
                        'request_id'        => $r->request_id,
                        'requested_by_id'   => optional(optional($r->employeeRS)->userRS)->full_name,
                        'requested_at'      => optional(Carbon::parse($r->requested_at))->format('M d, Y'),
                        'department'        => optional(optional($r->employeeRS)->deptRS)->name,
                        'notes'             => $r->notes,
                        'status'            => Status::getStatusText($r->status),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    public function getRequestsHistory()
    {
        try {
            $requests = BudgetRelease::with(['employeeRS', 'statusRS', 'departmentRS'])
                ->where('status', 15)
                ->orderBy('released_at', 'desc')->get();
            // dd($employees);
            return response()->json([
                'data' => $requests->map(function ($r) {
                    return [
                        'id'                => $r->id,
                        'release_id'        => $r->release_id,
                        'type'              => $r->type,
                        'amount'            => $r->amount,
                        'request_id'        => $r->request_id,
                        'requested_by_id'   => optional(optional($r->employeeRS)->userRS)->full_name,
                        'requested_at'      => optional(Carbon::parse($r->requested_at))->format('M d, Y'),
                        'released_by_id'   => optional(optional($r->employeeRS)->userRS)->full_name,
                        'released_at'      => optional(Carbon::parse($r->requested_at))->format('M d, Y'),
                        'department'        => optional($r->departmentRS)->name,
                        'status'            => Status::getStatusText($r->status),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function rejectRequest(Request $request, $id)
    {

        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'request_id' => 'required',
                'notes' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'messages' => $validator->errors()->all()
                ], 422);
            }

            $release = BudgetRelease::findOrFail($id);
            $release->notes = $request->notes;
            $release->status = 12;
            $release->save();

            $payroll = Payroll::find($request->request_id);
            $payroll->remarks = $request->notes;
            $payroll->status = 12;
            $payroll->save();

            DB::commit();

            return response()->json(['message' => 'Request is Rejected!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    public function approveRequest($id, $req_id)
    {
        try {
            DB::beginTransaction();

            $release = BudgetRelease::findOrFail($id);
            $release->notes = "released";
            $release->status = 15;
            $release->released_by_id = auth('')->user()->id;
            $release->released_at = now();
            $release->save();

            if ($release->type == 'Payroll') {
                $payroll = Payroll::find($req_id);
                $payroll->status = 13;
                $payroll->save();
            }

            DB::commit();

            return response()->json(['message' => 'Request is Released!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

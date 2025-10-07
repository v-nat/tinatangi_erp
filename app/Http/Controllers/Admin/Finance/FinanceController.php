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
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;

class FinanceController extends Controller
{
    //
    public function finance(){
        return view("pages.admin.finance.index");
    }
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
            $requests = BudgetRelease::with(['employeeRS', 'departmentRS', 'statusRS'])
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
                        'department'        => optional($r->departmentRS)->name,
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

    public function purchaseRequests()
    {
        try {
            $requests = PurchaseRequest::with(['employeeRS', 'deptRS', 'statusRS'])
                ->orderBy('requested_date', 'desc')->get();
            // dd($employees);
            return response()->json([
                'data' => $requests->map(function ($r) {
                    return [
                        'id'                => $r->id,
                        'type'              => $r->type,
                        'amount'            => $r->amount,
                        'department'        => optional($r->deptRS)->name,
                        'requested_by_id'   => optional(optional($r->employeeRS)->userRS)->full_name,
                        'requested_date'    => optional(Carbon::parse($r->requested_date))->format('M d, Y'),
                        'remarks'           => $r->remarks,
                        'status'            => Status::getStatusText($r->status),
                        'invoice_id'        => $r->invoice_id,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    public function getDetailsForViewing($id)
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.purchaseOrderDetail',
                'purchaseOrders.supplierRS',
                'purchaseOrders.purchaseOrderDetail.itemss',
                'purchaseOrders.purchaseOrderDetail.itemss.unit',
                'statusRS',
                'employeeRS',
                'supplierRS',
                'deptRS',
            ])->where('id', $id)->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        $mappedDetails = $order->purchaseOrderDetail->map(function ($detail) {
                            return [
                                'item_name'   => optional($detail->itemss)->name,
                                'item_unit'   => optional(optional($detail->itemss)->unit)->abbreviation,
                                'item_unit_name'   => optional(optional($detail->itemss)->unit)->name,
                                'quantity'    => (int)$detail->quantity,
                                'unit_price'  => (float)$detail->unit_price,
                                'total_amount' => (float)$detail->total_amount,
                            ];
                        });

                        return [
                            'purchase_order_id' => $order->purchase_orderId,

                            'details'           => $mappedDetails,
                        ];
                    });

                    return [
                        'id'             => $request_data->id,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'department'     => optional($request_data->deptRS)->name,
                        'remarks'        => $request_data->remarks,
                        'status'         => Status::statusAlert($request_data->status),
                        'total_amount'   => (float)$request_data->amount,
                        'invoice_id'     => $request_data->invoice_id,
                        'supplier_name'     => optional($request_data->supplierRS)->supplier_name,
                        'purchase_orders' => $mappedOrders,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function getRequestsHistory()
    {
        try {
            $requests = BudgetRelease::with(['employeeRS', 'statusRS', 'departmentRS'])
                ->where('status', 15)
                ->orderBy('released_at', 'desc')->get();
            // dd($requests);
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
                        'released_by_id'   => optional(optional($r->employeeRS)->userRS)->full_name ?? '',
                        'released_at'      => optional(Carbon::parse($r->released_at))->format('M d, Y') ?? '',
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
                $payroll = Payroll::findOrFail($req_id);
                $payroll->remarks = "budget released";
                $payroll->status = 13;
                $payroll->save();
            } else if ($release->type == 'Purchase Order') {
                $pr = PurchaseRequest::findOrFail($req_id);
                $pr->remarks = 'budget released';
                $pr->status = 18;
                $pr->save();
                $orderInstances = PurchaseOrder::where('purchase_request_id', $req_id)->pluck('id');
                foreach ($orderInstances as $orderInstance) {
                    $prpo = PurchaseOrder::where('id', $orderInstance)->first();
                    $prpo->type = 'Purchase Order';
                    $prpo->remarks = 'budget released';
                    $prpo->status = 18;
                    $prpo->save();
                    $prpod = PurchaseOrderDetail::where('id', $orderInstance)->first();
                    $prpod->status = 18;
                    $prpod->save();
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Request is Released!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
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


            if ($release->type == 'Payroll') {
                $payroll = Payroll::findOrFail($request->request_id);
                $payroll->remarks = $request->notes;
                $payroll->status = 12;
                $payroll->save();
            } else if ($release->type == 'Purchase Order') {
                $pr = PurchaseRequest::findOrFail($request->request_id);
                $pr->remarks = $request->notes;
                $pr->status = 12;
                $pr->save();

                $orderInstances = PurchaseOrder::where('purchase_request_id', $request->request_id)->pluck('id');
                foreach ($orderInstances as $orderInstance) {
                    $prpo = PurchaseOrder::where('id', $orderInstance)->first();
                    $prpo->remarks = $request->notes;
                    $prpo->status = 12;
                    $prpo->save();

                    $prpod = PurchaseOrderDetail::where('id', $orderInstance)->first();
                    $prpod->status = 12;
                    $prpod->save();
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Request is Rejected!'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

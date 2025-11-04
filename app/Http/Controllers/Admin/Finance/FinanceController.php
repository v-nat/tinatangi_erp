<?php

namespace App\Http\Controllers\Admin\Finance;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Invoice;
use App\Models\Payroll;
use Illuminate\Http\Request;
use App\Models\BudgetRelease;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FinanceController extends Controller
{
    public function finance()
    {
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

    public function getDashboardAnalytics(): JsonResponse
    {
        $kpis = [
            'pendingPayroll' => Payroll::where('status', 11)->sum('net_pay'),
            'pendingBudgets' => BudgetRelease::where('status', 14)->sum('amount'),
            'pendingPOs' => PurchaseRequest::where('status', 11)->sum('amount'),
            'pendingInvoices' => Invoice::where('status', 11)->sum('total_amount'),
        ];

        $budgetData = BudgetRelease::select(
            DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
            DB::raw("DATE_FORMAT(released_at, '%Y-%m') as month_year")
        )
            ->where('status', '!=', 11)
            ->where('released_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month_year')
            ->orderBy('month_year', 'ASC')
            ->get();

        $payrollData = DB::table('payrolls')
            ->join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->join('departments', 'employees.department', '=', 'departments.id')
            ->select('departments.name', DB::raw('COALESCE(SUM(payrolls.gross_pay), 0) as total_payroll'))
            ->where('payrolls.status', '!=', 11)
            ->groupBy('departments.name')
            ->get();

        $charts = [
            'budgetReleased' => [
                'labels' => $budgetData->pluck('month_year'),
                'data' => $budgetData->pluck('total_amount'),
            ],
            'payrollByDept' => [
                'labels' => $payrollData->pluck('name'),
                'data' => $payrollData->pluck('total_payroll'),
            ],
        ];

        $budgetsAwaitingRelease = BudgetRelease::with(['requestedBy:id,first_name,last_name', 'department:id,name'])
            ->where('status', 14)
            ->latest('requested_at')
            ->take(5)
            ->get()
            ->map(fn($budget) => [
                'requestor' => $budget->requestedBy->full_name ?? 'N/A',
                'department' => $budget->department->name ?? 'N/A',
                'amount' => $budget->amount,
            ]);

        $invoicesAwaitingApproval = Invoice::with(['supplier:id,name', 'purchaseRequest:id'])
            ->where('status', 11)
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($invoice) => [
                'supplier' => $invoice->supplier->name ?? 'N/A',
                'po_id' => $invoice->purchaseRequest->id ?? 'N/A',
                'total_amount' => $invoice->total_amount,
            ]);

        $data = [
            'kpis' => $kpis,
            'charts' => $charts,
            'tables' => [
                'budgetsAwaitingRelease' => $budgetsAwaitingRelease,
                'invoicesAwaitingApproval' => $invoicesAwaitingApproval,
            ]
        ];

        return response()->json($data);
    }

    public function getPendingRequests()
    {
        try {
            $requests = BudgetRelease::with(['employeeRS', 'departmentRS', 'statusRS'])
                ->where('status', 11)
                ->orderBy('requested_at', 'desc')->get();

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
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function purchaseRequests()
    {
        try {
            $requests = PurchaseRequest::with(['employeeRS', 'deptRS', 'statusRS'])
                ->whereNot('status', 27)
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
            return response()->json(['error' => $e->getMessage()], 500);
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
                'purchaseOrders.purchaseOrderDetail.itemss.unitRS',
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
                                'item_unit'   => optional(optional($detail->itemss)->unitRS)->abbreviation,
                                'item_unit_name'   => optional(optional($detail->itemss)->unitRS)->name,
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
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function getRequestsHistory()
    {
        try {
            $requests = BudgetRelease::with(['employeeRS', 'statusRS', 'departmentRS'])
                ->where('status', 15)
                ->orderBy('released_at', 'desc')->get();

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
            return response()->json(['error' => $e->getMessage()], 500);
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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

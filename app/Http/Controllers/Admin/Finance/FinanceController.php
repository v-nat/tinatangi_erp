<?php

namespace App\Http\Controllers\Admin\Finance;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\SalesReport;
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

    public function getDashboardAnalytics()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfSixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();

        $totalPayroll = Payroll::whereBetween('end_date', [$startOfMonth, $now])->sum('net_pay');

        $budgetReleased = BudgetRelease::whereBetween('released_at', [$startOfMonth, $now])->sum('amount');

        $poSpending = PurchaseOrderDetail::whereHas('purchaseOrder', function ($q) use ($startOfMonth, $now) {
            $q->whereBetween('order_date', [$startOfMonth, $now]);
        })->sum('total_amount');

        $pendingPRs = PurchaseRequest::where('status', 11)->count();

        $budgetData = BudgetRelease::select(
            DB::raw('SUM(amount) as total'),
            DB::raw("DATE_FORMAT(released_at, '%Y-%m') as month")
        )
            ->where('released_at', '>=', $startOfSixMonthsAgo)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $spendingData = PurchaseOrderDetail::whereHas('purchaseOrder', function ($q) use ($startOfSixMonthsAgo) {
            $q->where('order_date', '>=', $startOfSixMonthsAgo);
        })
            ->select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw("DATE_FORMAT(purchase_orders.order_date, '%Y-%m') as month")
            )
            ->join('purchase_orders', 'purchase_order_details.purchase_order_id', '=', 'purchase_orders.id') // Join to get order_date
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $budgetVsSpending = $this->formatMonthlyChartData(
            ['budget' => $budgetData, 'spending' => $spendingData],
            $startOfSixMonthsAgo
        );

        $spendingByDept = BudgetRelease::join('departments', 'budget_releases.department', '=', 'departments.id')
            ->select('departments.name as department_name', DB::raw('SUM(budget_releases.amount) as total'))
            ->groupBy('departments.name')
            ->orderBy('total', 'desc')
            ->get();

        $pendingBudgets = BudgetRelease::with('departmentRS')
            ->where('status', 14)
            ->orderBy('requested_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type' => $item->type,
                'amount' => $item->amount,
                'department' => $item->departmentRS->name ?? 'N/A',
            ]);

        $payrollOverview = Payroll::select(
            DB::raw("CONCAT(DATE_FORMAT(start_date, '%b %e'), ' - ', DATE_FORMAT(end_date, '%b %e')) as pay_period_label"),
            DB::raw('SUM(gross_pay) as gross'),
            DB::raw('SUM(deduction) as deductions'),
            DB::raw('SUM(net_pay) as net')
        )
            ->groupBy('pay_period_label', 'start_date')
            ->orderBy('start_date', 'desc')
            ->take(3)
            ->get()
            ->reverse();

        $salesStart = Carbon::today()->subDays(6);
        $salesDaily = SalesReport::select(
            DB::raw("DATE(reported_at) as report_date"),
            DB::raw("SUM(total_amount) as total_reported"),
            DB::raw("SUM(CASE WHEN status = 23 THEN total_amount ELSE 0 END) as total_approved")
        )
            ->whereNotNull('reported_at')
            ->whereDate('reported_at', '>=', $salesStart)
            ->groupBy('report_date')
            ->orderBy('report_date')
            ->get();

        $salesLabels = [];
        $reportedSeries = [];
        $approvedSeries = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $salesStart->copy()->addDays($i);
            $salesLabels[] = $day->format('M d');
            $match = $salesDaily->firstWhere('report_date', $day->toDateString());
            $reportedSeries[] = $match ? (float) $match->total_reported : 0;
            $approvedSeries[] = $match ? (float) $match->total_approved : 0;
        }

        $salesApprovedMonth = SalesReport::where('status', 23)
            ->whereBetween('reviewed_at', [$startOfMonth, $now])
            ->sum('total_amount');

        $salesPendingCount = SalesReport::where('status', 11)->count();
        $salesReportedToday = SalesReport::whereDate('reported_at', $now)->sum('total_amount');
        $salesApprovedToday = SalesReport::where('status', 23)
            ->whereDate('reviewed_at', $now)
            ->sum('total_amount');

        $budgetVariance = $budgetReleased - $poSpending;

        return response()->json([
            'kpis' => [
                'totalPayroll' => $totalPayroll,
                'budgetReleased' => $budgetReleased,
                'poSpending' => $poSpending,
                'pendingPRs' => $pendingPRs,
                'salesApproved' => $salesApprovedMonth,
                'salesPending' => $salesPendingCount,
                'salesReportedToday' => $salesReportedToday,
                'salesApprovedToday' => $salesApprovedToday,
                'budgetVariance' => $budgetVariance,
            ],
            'charts' => [
                'budgetVsSpending' => $budgetVsSpending,
                'spendingByDepartment' => [
                    'labels' => $spendingByDept->pluck('department_name'),
                    'series' => $spendingByDept->pluck('total'),
                ],
                'payrollOverview' => [
                    'labels' => $payrollOverview->pluck('pay_period_label'),
                    'series' => [
                        ['name' => 'Gross Pay', 'data' => $payrollOverview->pluck('gross')],
                        ['name' => 'Deductions', 'data' => $payrollOverview->pluck('deductions')],
                        ['name' => 'Net Pay', 'data' => $payrollOverview->pluck('net')],
                    ]
                ],
                'salesActivity' => [
                    'labels' => $salesLabels,
                    'series' => [
                        ['name' => 'Reported', 'data' => $reportedSeries],
                        ['name' => 'Approved', 'data' => $approvedSeries],
                    ],
                ],
            ],
            'tables' => [
                'pendingBudgets' => $pendingBudgets,
            ],
        ]);
    }

    private function formatMonthlyChartData($datasets, $startDate)
    {
        $months = [];
        $labels = [];
        $series = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $startDate->copy()->addMonths($i)->format('Y-m');
            $labels[] = $startDate->copy()->addMonths($i)->format('M');
            $months[$month] = $i;
        }

        foreach ($datasets as $key => $data) {
            $monthlyData = array_fill(0, 6, 0);
            foreach ($data as $item) {
                if (isset($months[$item->month])) {
                    $index = $months[$item->month];
                    $monthlyData[$index] = (float) $item->total;
                }
            }
            $series[] = [
                'name' => ucfirst($key),
                'data' => $monthlyData,
            ];
        }

        return ['labels' => $labels, 'series' => $series];
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
                                'pod_id'       => $detail->id,
                                'item_name'   => optional($detail->itemss)->name,
                                'item_unit'   => optional(optional($detail->itemss)->unit)->abbreviation,
                                'item_unit_name'   => optional(optional($detail->itemss)->unit)->name,
                                'is_perishable' => (bool) optional($detail->itemss)->is_perishable,
                                'quantity'    => (int)$detail->quantity,
                                'unit_price'  => (float)$detail->unit_price,
                                'total_amount' => (float)$detail->total_amount,
                                'status_code' => (int)$detail->status,
                                'status_text' => Status::getStatusText($detail->status),
                                'is_returned' => (int)$detail->status === 22,
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

    public function getBudgetSummary(): JsonResponse
    {
        $start = Carbon::today()->startOfMonth()->subMonths(5);

        $approved = BudgetRelease::select(
            DB::raw("DATE_FORMAT(requested_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as total')
        )
            ->whereNotNull('requested_at')
            ->where('requested_at', '>=', $start)
            ->groupBy('month')
            ->get();

        $released = BudgetRelease::select(
            DB::raw("DATE_FORMAT(released_at, '%Y-%m') as month"),
            DB::raw('SUM(amount) as total')
        )
            ->whereNotNull('released_at')
            ->where('released_at', '>=', $start)
            ->groupBy('month')
            ->get();

        $labels = [];
        $approvedSeries = [];
        $releasedSeries = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->format('M');

            $approvedMatch = $approved->firstWhere('month', $key);
            $releasedMatch = $released->firstWhere('month', $key);

            $approvedSeries[] = $approvedMatch ? (float) $approvedMatch->total : 0;
            $releasedSeries[] = $releasedMatch ? (float) $releasedMatch->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'series' => [
                ['name' => 'Approved', 'data' => $approvedSeries],
                ['name' => 'Released', 'data' => $releasedSeries],
            ],
        ]);
    }
}

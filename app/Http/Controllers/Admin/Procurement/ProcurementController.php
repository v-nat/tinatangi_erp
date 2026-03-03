<?php

namespace App\Http\Controllers\Admin\Procurement;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProcurementController extends Controller
{
    public function index()
    {
        return view("pages.admin.procurement.index");
    }

    public function createPR()
    {
        return view("pages.admin.procurement.create-purchase-request");
    }
    public function supplier()
    {
        return view("pages.admin.procurement.manage-supplier");
    }
    public function purchaseOrders()
    {
        return view("pages.admin.procurement.purchase-orders");
    }

    public function getDashboardAnalytics()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $completedStatuses = [23];
        $closedStatuses = [23, 19, 12];

        $pendingPRCount = PurchaseRequest::where('status', 11)->count();

        $openPOCount = PurchaseOrder::whereNotIn('status', $closedStatuses)->count();

        $overduePOCount = PurchaseOrder::whereNotIn('status', $closedStatuses)
            ->whereNotNull('delivery_date')
            ->whereDate('delivery_date', '<', $today)
            ->count();

        $monthSpend = DB::table('purchase_orders')
            ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
            ->whereIn('purchase_orders.status', $completedStatuses)
            ->whereBetween('purchase_orders.created_at', [$monthStart, $monthEnd])
            ->sum('purchase_order_details.total_amount');

        $recentPRs = PurchaseRequest::with(['employeeRS.userRS'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($pr) {
                return [
                    'id' => $pr->id,
                    'reference' => $pr->order_id ?? ('PR-' . str_pad($pr->id, 5, '0', STR_PAD_LEFT)),
                    'requested_at' => optional($pr->created_at)->toDateTimeString(),
                    'requested_by' => optional(optional($pr->employeeRS)->userRS)->full_name,
                    'status_html' => Status::getStatusText($pr->status),
                ];
            })
            ->values();

        $recentPOs = PurchaseOrder::with(['supplierRS', 'purchaseOrderDetail'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($po) use ($closedStatuses) {
                $totalAmount = $po->purchaseOrderDetail->sum('total_amount');
                $isOpen = !in_array($po->status, $closedStatuses, true);
                $isOverdue = $isOpen && $po->delivery_date && Carbon::parse($po->delivery_date)->isPast();

                return [
                    'id' => $po->id,
                    'reference' => $po->purchase_orderId ?? ('PO-' . str_pad($po->id, 5, '0', STR_PAD_LEFT)),
                    'order_date' => $po->order_date,
                    'delivery_date' => $po->delivery_date,
                    'supplier_name' => optional($po->supplierRS)->supplier_name,
                    'status_html' => Status::getStatusText($po->status),
                    'total_amount' => (float) $totalAmount,
                    'is_overdue' => $isOverdue,
                ];
            })
            ->values();

        $topSuppliers = DB::table('suppliers')
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
            ->whereIn('purchase_orders.status', $completedStatuses)
            ->whereBetween('purchase_orders.created_at', [$monthStart, $monthEnd])
            ->select('suppliers.supplier_name as supplier_name', DB::raw('SUM(purchase_order_details.total_amount) as total'))
            ->groupBy('suppliers.supplier_name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($row) {
                return [
                    'supplier_name' => $row->supplier_name,
                    'total' => (float) $row->total,
                ];
            })
            ->values();

        return response()->json([
            'kpis' => [
                'pendingPR' => $pendingPRCount,
                'openPO' => $openPOCount,
                'overduePO' => $overduePOCount,
                'monthSpend' => (float) $monthSpend,
            ],
            'recentPRs' => $recentPRs,
            'recentPOs' => $recentPOs,
            'topSuppliers' => $topSuppliers,
        ]);
    }

    public function getSpendForecast(Request $request)
    {
        $completedStatuses = [23];
        $currentYear = Carbon::now()->year;
        $view        = $request->get('view', 'period'); // 'period' or 'yearly'

        // ── Yearly view: 5-year annual totals ────────────────────────────────
        if ($view === 'yearly') {
            $spendData = [];
            for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                $start = Carbon::create($y, 1, 1)->startOfMonth();
                $end   = Carbon::create($y, 12, 1)->endOfMonth();

                $spend = DB::table('purchase_orders')
                    ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
                    ->whereIn('purchase_orders.status', $completedStatuses)
                    ->whereBetween('purchase_orders.created_at', [$start, $end])
                    ->whereNull('purchase_orders.deleted_at')
                    ->whereNull('purchase_order_details.deleted_at')
                    ->sum('purchase_order_details.total_amount');

                $spendData[] = [
                    'label' => (string) $y,
                    'spend' => (float) $spend,
                ];
            }

            // 3-year moving average → forecast next year
            $last3           = array_slice($spendData, -3);
            $forecastedSpend = count($last3) > 0
                ? array_sum(array_column($last3, 'spend')) / count($last3)
                : 0;
            $forecastedLabel = (string) ($currentYear + 1);

            // Top re-ordered items across all 5 years
            $topItems = DB::table('purchase_order_details')
                ->join('purchase_orders', 'purchase_order_details.purchase_order_id', '=', 'purchase_orders.id')
                ->join('items', 'purchase_order_details.item_id', '=', 'items.id')
                ->whereIn('purchase_orders.status', $completedStatuses)
                ->whereBetween('purchase_orders.created_at', [
                    Carbon::create($currentYear - 4, 1, 1)->startOfMonth(),
                    Carbon::create($currentYear, 12, 1)->endOfMonth(),
                ])
                ->whereNull('purchase_orders.deleted_at')
                ->whereNull('purchase_order_details.deleted_at')
                ->select(
                    'items.name as item_name',
                    DB::raw('COUNT(DISTINCT purchase_orders.id) as order_count'),
                    DB::raw('SUM(purchase_order_details.quantity) as total_qty')
                )
                ->groupBy('items.name')
                ->orderByDesc('order_count')
                ->take(5)
                ->get()
                ->map(fn($row) => [
                    'item_name'   => $row->item_name,
                    'order_count' => (int) $row->order_count,
                    'total_qty'   => (int) $row->total_qty,
                ])
                ->values();

            return response()->json([
                'spendData'            => $spendData,
                'granularity'          => 'yearly',
                'forecastedNextPeriod' => (float) $forecastedSpend,
                'forecastedLabel'      => $forecastedLabel,
                'topReorderedItems'    => $topItems,
            ]);
        }

        // ── Period view: quarterly or monthly for selected year/range ─────────
        $year       = (int) $request->get('year', $currentYear);
        $year       = max($currentYear - 4, min($currentYear, $year));
        $monthStart = (int) $request->get('month_start', 1);
        $monthStart = max(1, min(12, $monthStart));
        $monthEnd   = (int) $request->get('month_end', 12);
        $monthEnd   = max($monthStart, min(12, $monthEnd));

        // Full-year range → quarterly bars; partial range → monthly bars
        $granularity = ($monthStart === 1 && $monthEnd === 12) ? 'quarterly' : 'monthly';

        $spendData = [];

        if ($granularity === 'quarterly') {
            for ($q = 1; $q <= 4; $q++) {
                $qMonthStart = ($q - 1) * 3 + 1;
                $qMonthEnd   = $q * 3;
                $start = Carbon::create($year, $qMonthStart, 1)->startOfMonth();
                $end   = Carbon::create($year, $qMonthEnd,   1)->endOfMonth();

                $spend = DB::table('purchase_orders')
                    ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
                    ->whereIn('purchase_orders.status', $completedStatuses)
                    ->whereBetween('purchase_orders.created_at', [$start, $end])
                    ->whereNull('purchase_orders.deleted_at')
                    ->whereNull('purchase_order_details.deleted_at')
                    ->sum('purchase_order_details.total_amount');

                $spendData[] = [
                    'label' => "Q{$q} {$year}",
                    'spend' => (float) $spend,
                ];
            }

            // 2-quarter moving average → forecast next quarter
            $last2           = array_slice($spendData, -2);
            $forecastedSpend = count($last2) > 0
                ? array_sum(array_column($last2, 'spend')) / count($last2)
                : 0;

            $currentQ        = (int) ceil(Carbon::now()->month / 3);
            $nextQ           = ($currentQ % 4) + 1;
            $nextQYear       = $nextQ === 1 ? $year + 1 : $year;
            $forecastedLabel = "Q{$nextQ} {$nextQYear}";
        } else {
            for ($m = $monthStart; $m <= $monthEnd; $m++) {
                $start = Carbon::create($year, $m, 1)->startOfMonth();
                $end   = Carbon::create($year, $m, 1)->endOfMonth();

                $spend = DB::table('purchase_orders')
                    ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
                    ->whereIn('purchase_orders.status', $completedStatuses)
                    ->whereBetween('purchase_orders.created_at', [$start, $end])
                    ->whereNull('purchase_orders.deleted_at')
                    ->whereNull('purchase_order_details.deleted_at')
                    ->sum('purchase_order_details.total_amount');

                $spendData[] = [
                    'label' => Carbon::create($year, $m, 1)->format('M Y'),
                    'spend' => (float) $spend,
                ];
            }

            // 3-month moving average → forecast month after range
            $last3           = array_slice($spendData, -3);
            $forecastedSpend = count($last3) > 0
                ? array_sum(array_column($last3, 'spend')) / count($last3)
                : 0;
            $forecastedLabel = Carbon::create($year, $monthEnd, 1)->addMonth()->format('M Y');
        }

        // Top re-ordered items scoped to the selected period
        $periodStart = Carbon::create($year, $monthStart, 1)->startOfMonth();
        $periodEnd   = Carbon::create($year, $monthEnd,   1)->endOfMonth();

        $topItems = DB::table('purchase_order_details')
            ->join('purchase_orders', 'purchase_order_details.purchase_order_id', '=', 'purchase_orders.id')
            ->join('items', 'purchase_order_details.item_id', '=', 'items.id')
            ->whereIn('purchase_orders.status', $completedStatuses)
            ->whereBetween('purchase_orders.created_at', [$periodStart, $periodEnd])
            ->whereNull('purchase_orders.deleted_at')
            ->whereNull('purchase_order_details.deleted_at')
            ->select(
                'items.name as item_name',
                DB::raw('COUNT(DISTINCT purchase_orders.id) as order_count'),
                DB::raw('SUM(purchase_order_details.quantity) as total_qty')
            )
            ->groupBy('items.name')
            ->orderByDesc('order_count')
            ->take(5)
            ->get()
            ->map(fn($row) => [
                'item_name'   => $row->item_name,
                'order_count' => (int) $row->order_count,
                'total_qty'   => (int) $row->total_qty,
            ])
            ->values();

        return response()->json([
            'spendData'            => $spendData,
            'granularity'          => $granularity,
            'forecastedNextPeriod' => (float) $forecastedSpend,
            'forecastedLabel'      => $forecastedLabel,
            'topReorderedItems'    => $topItems,
            'selectedYear'         => $year,
            'selectedMonthStart'   => $monthStart,
            'selectedMonthEnd'     => $monthEnd,
        ]);
    }

    public function purchaseOrdersList()
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.supplierRS',
                'statusRS',
                'employeeRS',
                'supplierRS',
                'deptRS',
            ])->whereNot('status', 11)->WhereNot('status', 27)
                ->orderBy('requested_date', 'desc')->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        return [
                            'purchase_order_id' => $order->purchase_orderId,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,
                            'supplier_name'     => optional($order->supplierRS)->supplier_name,
                        ];
                    });

                    return [
                        'id'             => $request_data->id,
                        'type'           => $request_data->type,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'remarks'        => $request_data->remarks,
                        'status'         => Status::getStatusText($request_data->status),
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

    public function purchaseRequestsList()
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.supplierRS',
                'statusRS',
                'employeeRS',
                'supplierRS',
                'deptRS',
            ])->where('status', 27)->orWhere('status', 11)->orWhere('status', 23)
                ->orderBy('requested_date', 'desc')->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        return [
                            'purchase_order_id' => $order->purchase_orderId,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,
                            'supplier_name'     => optional($order->supplierRS)->supplier_name,
                        ];
                    });

                    return [
                        'id'             => $request_data->id,
                        'type'           => $request_data->type,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'remarks'        => $request_data->remarks,
                        'status'         => Status::getStatusText($request_data->status),
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

    public function getRestockData($id)
    {
        try {
            $purchaseRequest = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.purchaseOrderDetail',
                'purchaseOrders.supplierRS',
                'purchaseOrders.purchaseOrderDetail.itemss',
                'purchaseOrders.purchaseOrderDetail.itemss.category',
                'purchaseOrders.purchaseOrderDetail.itemss.unit',
                'purchaseOrders.purchaseOrderDetail.itemss.supplier',
                'supplierRS',
            ])->find($id);
            if (!$purchaseRequest) {
                return response()->json(['error' => 'Purchase Request not found.'], 404);
            }
            $mappedOrders = $purchaseRequest->purchaseOrders->map(function ($order) {

                $mappedDetails = $order->purchaseOrderDetail->map(function ($detail) {
                    return [
                        'item_id'       => $detail->item_id,
                        'item_name'     => optional($detail->itemss)->name,
                        'item_unit'     => optional(optional($detail->itemss)->unit)->name,
                        'category_id'   => $detail->category_id,
                        'supplier_id'   => optional($detail->itemss)->supplier_id,
                        'supplier_name' => optional(optional($detail->itemss)->supplier)->supplier_name,
                        'category_name' => optional(optional($detail->itemss)->category)->name,
                        'quantity'      => (int)$detail->quantity,
                        'unit_price'    => (float)$detail->unit_price,
                        'total_amount'  => (float)$detail->total_amount,
                    ];
                });

                return [
                    'purchase_order_id' => $order->purchase_orderId,
                    'supplier_id'       => $order->supplier_id,
                    'supplier_name'     => optional($order->supplierRS)->supplier_name,
                    'details'           => $mappedDetails,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id'              => $purchaseRequest->id,
                    'total_amount'    => (float)$purchaseRequest->amount,
                    'supplier_id'     => $purchaseRequest->supplier_id,
                    'supplier_name'   => optional($purchaseRequest->supplierRS)->supplier_name,
                    'purchase_orders' => $mappedOrders,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected server error occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}

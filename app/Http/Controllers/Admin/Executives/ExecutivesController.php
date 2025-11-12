<?php

namespace App\Http\Controllers\Admin\Executives;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Leave;
use App\Models\Booking;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\BudgetRelease;
use App\Models\OrderItem;
use App\Models\SalesReport;
use App\Models\ServiceFeedback;
use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Status;

class ExecutivesController extends Controller
{
    public function index()
    {
        return view('pages.admin.executives.dashboard');
    }

    public function analytics()
    {
        return response()->json($this->compileAnalytics());
    }

    private function compileAnalytics(): array
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        $statusNames = [
            'Pending',
            'Rejected',
            'Approved',
            'On Process',
            'Released',
            'Delivered',
            'Partial Delivered',
            'Approved - Pending Dispatch',
            'Rejected - Supplier',
            'Accepted - Supplier',
            'Pending - Supplier',
            'Return',
            'Completed',
            'On Stock',
            'Low Stock',
            'Out of Stock',
            'Pending Restock',
            'In Queue',
            'In Prep',
            'Ready',
            'Voided',
            'Loss',
            'Growth',
            'Hidden',
            'Displayed',
            'Redeliver - Supplier',
            'Cancelled - Supplier',
        ];

        $statusMap = Status::whereIn('status', $statusNames)->pluck('id', 'status');
        $resolveStatus = function (string $label, int $fallback) use ($statusMap): int {
            return (int) $statusMap->get($label, $fallback);
        };

        $pendingStatus = $resolveStatus('Pending', 11);
        $rejectedStatus = $resolveStatus('Rejected', 12);
        $approvedStatus = $resolveStatus('Approved', 13);
        $onProcessStatus = $resolveStatus('On Process', 14);
        $deliveredStatus = $resolveStatus('Delivered', 16);
        $completedStatus = $resolveStatus('Completed', 23);
        $lowStockStatus = $resolveStatus('Low Stock', 25);
        $outOfStockStatus = $resolveStatus('Out of Stock', 26);
        $inQueueStatus = $resolveStatus('In Queue', 28);
        $inPrepStatus = $resolveStatus('In Prep', 29);
        $readyStatus = $resolveStatus('Ready', 30);
        $voidedStatus = $resolveStatus('Voided', 31);
        $rejectedSupplierStatus = $resolveStatus('Rejected - Supplier', 19);
        $pendingSupplierStatus = $resolveStatus('Pending - Supplier', 21);

        $completedStatuses = [$completedStatus];
        $inProgressStatuses = [$inQueueStatus, $inPrepStatus, $readyStatus];
        $voidedStatuses = [$voidedStatus];
        $cancelledStatuses = array_merge($voidedStatuses, [$rejectedStatus]);

        $hr = [
            'totalEmployees' => Employee::whereNull('deleted_at')->count(),
            'newHires30'     => Employee::whereNull('deleted_at')
                ->where('created_at', '>=', $now->copy()->subDays(30))
                ->count(),
            'pendingLeaves'  => Leave::where('status', $pendingStatus)->count(),
            'pendingOvertime'=> Overtime::where('status', $pendingStatus)->count(),
        ];

        $salesToday = Order::whereDate('created_at', $today)
            ->whereIn('status', $completedStatuses)
            ->sum('total_amount');

        $completedOrders = Order::whereDate('created_at', $today)
            ->whereIn('status', $completedStatuses)
            ->count();

        $operationsTopProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.created_at', $today)
            ->whereIn('orders.status', $completedStatuses)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(fn ($row) => [
                'name'  => $row->name,
                'total' => (int) $row->total_sold,
            ])
            ->values();

        $operations = [
            'salesToday'       => round($salesToday, 2),
            'completedOrders'  => $completedOrders,
            'avgOrderValue'    => $completedOrders > 0 ? round($salesToday / $completedOrders, 2) : 0,
            'ordersInProgress' => Order::whereDate('created_at', $today)
                ->whereIn('status', $inProgressStatuses)
                ->count(),
            'voidedOrders'     => Order::whereDate('created_at', $today)
                ->whereIn('status', $voidedStatuses)
                ->count(),
            'topProducts'      => $operationsTopProducts,
        ];

        $inventoryCritical = InventoryItem::with(['itemss', 'unit'])
            ->whereIn('status', [$lowStockStatus, $outOfStockStatus])
            ->orderBy('stock_level')
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'name'       => optional($item->itemss)->name ?? 'Unnamed Item',
                'stock'      => (int) $item->stock_level,
                'unit'       => optional($item->unit)->abbreviation ?? '',
            ])
            ->values();

        $inventory = [
            'toReceive'   => PurchaseRequest::where('status', $deliveredStatus)->count(),
            'totalSkus'   => InventoryItem::distinct()->count('item_id'),
            'lowStock'    => InventoryItem::where('status', $lowStockStatus)->count(),
            'outOfStock'  => InventoryItem::where('status', $outOfStockStatus)->count(),
            'criticalItems' => $inventoryCritical,
        ];

        $financeBudgetReleased = BudgetRelease::whereBetween('released_at', [$startOfMonth, $now])->sum('amount');
        $financePoSpending = PurchaseOrderDetail::whereHas('purchaseOrder', function ($q) use ($startOfMonth, $now) {
            $q->whereBetween('order_date', [$startOfMonth, $now]);
        })->sum('total_amount');

        $finance = [
            'payrollNet'      => Payroll::whereBetween('end_date', [$startOfMonth, $now])->sum('net_pay'),
            'budgetReleased'  => $financeBudgetReleased,
            'poSpending'      => $financePoSpending,
            'budgetVariance'  => $financeBudgetReleased - $financePoSpending,
            'pendingBudgets'  => BudgetRelease::where('status', $onProcessStatus)->count(),
            'pendingPRs'      => PurchaseRequest::where('status', $pendingStatus)->count(),
            'salesApproved'   => SalesReport::where('status', $completedStatus)
                ->whereBetween('reviewed_at', [$startOfMonth, $now])
                ->sum('total_amount'),
        ];

        $closedStatuses = [$completedStatus, $rejectedSupplierStatus, $rejectedStatus];
        $procurementTopSuppliers = DB::table('suppliers')
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->join('purchase_order_details', 'purchase_orders.id', '=', 'purchase_order_details.purchase_order_id')
            ->whereIn('purchase_orders.status', $completedStatuses)
            ->whereBetween('purchase_orders.created_at', [$startOfMonth, $now])
            ->select('suppliers.supplier_name as supplier_name', DB::raw('SUM(purchase_order_details.total_amount) as total'))
            ->groupBy('suppliers.supplier_name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($row) => [
                'supplier' => $row->supplier_name,
                'total'    => (float) $row->total,
            ])
            ->values();

        $procurement = [
            'pendingPR'   => $finance['pendingPRs'],
            'openPO'      => PurchaseOrder::whereNotIn('status', $closedStatuses)->count(),
            'overduePO'   => PurchaseOrder::whereNotIn('status', $closedStatuses)
                ->whereNotNull('delivery_date')
                ->whereDate('delivery_date', '<', $today)
                ->count(),
            'monthSpend'  => PurchaseOrderDetail::whereHas('purchaseOrder', function ($query) use ($startOfMonth, $now, $completedStatuses) {
                    $query->whereIn('status', $completedStatuses)
                        ->whereBetween('created_at', [$startOfMonth, $now]);
                })->sum('total_amount'),
            'topSuppliers'=> $procurementTopSuppliers,
        ];

        $crmAverageRating = ServiceFeedback::avg('overall_rating');

        $crmUpcomingList = Booking::with('tableForReservation')
            ->whereDate('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(5)
            ->get()
            ->map(fn ($booking) => [
                'name'   => $booking->name,
                'date'   => optional($booking->date) ? Carbon::parse($booking->date)->format('M d, Y') : null,
                'time'   => $booking->time,
                'people' => (int) $booking->people,
                'table'  => optional($booking->tableForReservation)->name,
            ])
            ->values();

        $crm = [
            'upcomingBookings' => Booking::whereDate('date', '>=', $today)
                ->whereIn('status', [$approvedStatus, $completedStatus])
                ->count(),
            'pendingBookings'  => Booking::where('status', $pendingStatus)->count(),
            'pendingFeedback'  => ServiceFeedback::where('status', $resolveStatus('Hidden', 34))
                ->whereColumn('created_at', 'updated_at')
                ->count(),
            'averageRating'    => $crmAverageRating ? round($crmAverageRating, 2) : null,
            'upcomingList'     => $crmUpcomingList,
        ];

        $summary = [
            'revenueToday'      => $operations['salesToday'],
            'budgetReleased'    => $finance['budgetReleased'],
            'pendingApprovals'  => $finance['pendingBudgets'] + $finance['pendingPRs'] + $procurement['overduePO'],
            'workforceCount'    => $hr['totalEmployees'],
            'inventoryAlerts'   => $inventory['lowStock'] + $inventory['outOfStock'],
            'customerSatisfaction' => $crm['averageRating'],
        ];

        $charts = [
            'operationsTopProducts' => [
                'labels' => $operationsTopProducts->pluck('name')->all(),
                'series' => [
                    [
                        'name' => 'Units Sold',
                        'data' => $operationsTopProducts->pluck('total')->map(fn ($value) => (int) $value)->all(),
                    ],
                ],
            ],
            'financeBudgetVsSpend' => [
                'categories' => ['Budget Released', 'PO Spending', 'Variance'],
                'series' => [
                    [
                        'name' => 'Amount',
                        'data' => [
                            round($finance['budgetReleased'], 2),
                            round($finance['poSpending'], 2),
                            round($finance['budgetVariance'], 2),
                        ],
                    ],
                ],
            ],
            'procurementTopSuppliers' => [
                'labels' => $procurementTopSuppliers->pluck('supplier')->all(),
                'series' => [
                    [
                        'name' => 'Total Spend',
                        'data' => $procurementTopSuppliers->pluck('total')->map(fn ($value) => round($value, 2))->all(),
                    ],
                ],
            ],
            'crmAverageRating' => [
                'value' => $crm['averageRating'] ?? 0,
                'percentage' => $crm['averageRating'] ? round(($crm['averageRating'] / 5) * 100, 2) : 0,
            ],
            'inventoryAlerts' => [
                'labels' => ['Low Stock', 'Out of Stock'],
                'series' => [
                    (int) ($inventory['lowStock'] ?? 0),
                    (int) ($inventory['outOfStock'] ?? 0),
                ],
            ],
        ];

        $operations['topProducts'] = $operationsTopProducts->toArray();
        $inventory['criticalItems'] = $inventoryCritical->toArray();
        $procurement['topSuppliers'] = $procurementTopSuppliers->toArray();
        $crm['upcomingList'] = $crmUpcomingList->toArray();

        return compact(
            'summary',
            'hr',
            'operations',
            'inventory',
            'finance',
            'procurement',
            'crm',
            'charts'
        );
    }
}

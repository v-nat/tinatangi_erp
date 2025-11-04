<?php

namespace App\Http\Controllers\Admin\Operations;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Status;
use App\Models\OrderItem;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OperationsController extends Controller
{
    public function index() {
        return view("pages.admin.operations.dashboard");
    }
    public function pos() {
        $userPos = Auth::user()->employeeRS->empPosition->id;
        if ($userPos == 16 || $userPos == 10) {
            return view("pages.admin.operations.point-of-sales");
        }
        return redirect()->back()->with('error', "You have no access to this page.");
    }
    public function kds() {
        $userDept = strtolower(Auth::user()->employeeRS->deptRS->name);
        if ($userDept != 'kitchen department') {
            return redirect()->back()->with('error', "You have no access to this page.");
        }
        return view('pages.admin.operations.kitchen-display');
    }

    public function getDashboardAnalytics()
    {
        $today = Carbon::today();

        $salesToday = Order::whereDate('created_at', $today)
                           ->whereNotIn('status', [12, 31])
                           ->sum('total_amount');

        $ordersToday = Order::whereDate('created_at', $today)
                            ->whereNotIn('status', [12, 31])
                            ->count();

        $avgOrderValue = $ordersToday > 0 ? $salesToday / $ordersToday : 0;

        $pendingOrders = Order::whereIn('status', [28, 29])->count();

        $topProducts = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->whereHas('orderRS', function($q) use ($today) {
                $q->whereDate('created_at', $today);
            })
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        $orderStatusCounts = Order::select('status', DB::raw('count(*) as total'))
            ->whereIn('status', [28, 29, 30])
            ->groupBy('status')
            ->pluck('total', 'status');

        $liveStatus = [
            'in_queue' => $orderStatusCounts->get(28, 0),
            'in_prep' => $orderStatusCounts->get(29, 0),
            'ready' => $orderStatusCounts->get(30, 0),
        ];

        $lowStockItems = InventoryItem::with('item')
            ->whereIn('status', [25, 26])
            ->orderBy('status', 'desc')
            ->take(10)
            ->get()
            ->map(fn($invItem) => [
                'name' => $invItem->item->name ?? 'Unknown Item',
                'stock_level' => $invItem->stock_level,
                'status_html' => Status::getStatusText($invItem->status)
            ]);


        return response()->json([
            'kpis' => [
                'salesToday' => $salesToday,
                'ordersToday' => $ordersToday,
                'avgOrderValue' => $avgOrderValue,
                'pendingOrders' => $pendingOrders,
            ],
            'charts' => [
                'topProducts' => [
                    'labels' => $topProducts->pluck('name'),
                    'series' => $topProducts->pluck('total_sold'),
                ]
            ],
            'tables' => [
                'liveStatus' => $liveStatus,
                'lowStockItems' => $lowStockItems,
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin\Operations;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Status;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\BestSellerService;

class OperationsController extends Controller
{
    protected BestSellerService $bestSellerService;

    public function __construct(BestSellerService $bestSellerService)
    {
        $this->bestSellerService = $bestSellerService;
    }

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

    public function getDashboardAnalytics(Request $request)
    {
        $today = Carbon::today();

        $completedStatuses = [23];
        $inProgressStatuses = [28, 29, 30];
        $voidedStatuses = [31];
        $cancelledStatuses = array_merge($voidedStatuses, [12]);

        $salesToday = Order::whereDate('created_at', $today)
            ->whereIn('status', $completedStatuses)
            ->sum('total_amount');

        $completedOrders = Order::whereDate('created_at', $today)
            ->whereIn('status', $completedStatuses)
            ->count();

        $totalOrders = Order::whereDate('created_at', $today)
            ->whereNotIn('status', $cancelledStatuses)
            ->count();

        $avgOrderValue = $completedOrders > 0 ? $salesToday / $completedOrders : 0;

        $inProgressOrders = Order::whereDate('created_at', $today)
            ->whereIn('status', $inProgressStatuses)
            ->count();

        $voidedOrders = Order::whereDate('created_at', $today)
            ->whereIn('status', $voidedStatuses)
            ->count();

        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.created_at', $today)
            ->whereIn('orders.status', $completedStatuses)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $orderTypeCounts = Order::select('order_type', DB::raw('count(*) as total'))
            ->whereDate('created_at', $today)
            ->whereNotIn('status', $cancelledStatuses)
            ->groupBy('order_type')
            ->get();

        $orderStatusCounts = Order::select('status', DB::raw('count(*) as total'))
            ->whereDate('created_at', $today)
            ->whereIn('status', $inProgressStatuses)
            ->groupBy('status')
            ->pluck('total', 'status');

        $liveStatus = [
            'in_queue' => $orderStatusCounts->get(28, 0),
            'in_prep' => $orderStatusCounts->get(29, 0),
            'ready' => $orderStatusCounts->get(30, 0),
        ];

        $lowStockProducts = Product::with(['ingredients' => function ($query) {
                $query->withPivot('quantity_used');
            }, 'ingredients.item', 'statusRS'])
            ->withCount('ingredients')
            ->get()
            ->map(function (Product $product) {
                $availableServings = $product->calculateAvailableServings();
                return [
                    'product' => $product,
                    'servings' => $availableServings,
                ];
            })
            ->filter(fn ($data) => $data['servings'] <= 5)
            ->sortBy('servings')
            ->take(10)
            ->map(function ($data) {
                /** @var \App\Models\Product $product */
                $product = $data['product'];
                $statusHtml = Status::getStatusText($product->status);

                return [
                    'name' => $product->name,
                    'stock_level' => $data['servings'],
                    'status_html' => $statusHtml,
                ];
            })
            ->values();

        $bestSellerLimit = (int) $request->query('best_seller_limit', 3);
        if ($bestSellerLimit <= 0) {
            $bestSellerLimit = 3;
        }
        $bestSellerLimit = min($bestSellerLimit, 10);

        $weeklyBestSellers = $this->bestSellerService->getWeeklyBestSellers($bestSellerLimit);
        $monthlyBestSellers = $this->bestSellerService->getMonthlyBestSellers($bestSellerLimit);

        $featuredProductIds = collect([$weeklyBestSellers, $monthlyBestSellers])
            ->flatMap(function ($period) {
                return collect($period['categories'] ?? [])
                    ->flatMap(fn ($category) => collect($category['items'] ?? [])->pluck('product_id'));
            })
            ->unique()
            ->values()
            ->all();

        $weeklyTrend = $this->bestSellerService->getProductTrend($featuredProductIds, 'weekly', 8);
        $monthlyTrend = $this->bestSellerService->getProductTrend($featuredProductIds, 'monthly', 6);

        return response()->json([
            'kpis' => [
                'salesToday' => round($salesToday, 2),
                'totalOrders' => $totalOrders,
                'completedOrders' => $completedOrders,
                'avgOrderValue' => round($avgOrderValue, 2),
                'inProgressOrders' => $inProgressOrders,
                'voidedOrders' => $voidedOrders,
            ],
            'charts' => [
                'topProducts' => [
                    'labels' => $topProducts->pluck('name'),
                    'series' => $topProducts->pluck('total_sold')->map(fn ($count) => (int) $count),
                ],
                'orderTypes' => [
                    'labels' => $orderTypeCounts->pluck('order_type')->map(function ($type) {
                        return ucwords(str_replace('-', ' ', $type));
                    }),
                    'series' => $orderTypeCounts->pluck('total')->map(fn ($count) => (int) $count),
                ],
            ],
            'tables' => [
                'liveStatus' => $liveStatus,
                'lowStockItems' => $lowStockProducts,
            ],
            'bestSellers' => [
                'weekly' => $weeklyBestSellers,
                'monthly' => $monthlyBestSellers,
            ],
            'trends' => [
                'weekly' => $weeklyTrend,
                'monthly' => $monthlyTrend,
            ],
            'generated_at' => Carbon::now()->toIso8601String(),
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BestSellerService
{
    protected const DEFAULT_LIMIT = 3;
    protected const MAX_LIMIT = 10;
    protected const MAX_WEEKLY_PERIODS = 12;
    protected const MAX_MONTHLY_PERIODS = 12;

    protected array $completedStatuses = [23];

    public function getWeeklyBestSellers(?int $limit = null): array
    {
        $limit = $this->normalizeLimit($limit);

        $now = Carbon::now();
        $start = $now->copy()->startOfWeek();
        $end = $now->copy()->endOfWeek();

        $results = $this->fetchCategorySales($start, $end);

        return [
            'type' => 'weekly',
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'label' => $this->formatRangeLabel($start, $end),
            'limit' => $limit,
            'categories' => $this->transformCategoryResults($results, $limit),
        ];
    }

    public function getMonthlyBestSellers(?int $limit = null): array
    {
        $limit = $this->normalizeLimit($limit);

        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end = $now->copy()->endOfMonth();

        $results = $this->fetchCategorySales($start, $end);

        return [
            'type' => 'monthly',
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'label' => $this->formatRangeLabel($start, $end),
            'limit' => $limit,
            'categories' => $this->transformCategoryResults($results, $limit),
        ];
    }

    public function getBestSellersForRange(Carbon $start, Carbon $end, ?int $limit = null): array
    {
        $limit = $this->normalizeLimit($limit);

        $results = $this->fetchCategorySales($start, $end);

        return [
            'type' => 'custom',
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'label' => $this->formatRangeLabel($start, $end),
            'limit' => $limit,
            'categories' => $this->transformCategoryResults($results, $limit),
        ];
    }

    public function getProductTrend(array $productIds, string $frequency = 'weekly', int $periods = 8, string $metric = 'units'): array
    {
        $productIds = array_values(array_unique(array_filter($productIds, fn($id) => $id !== null)));

        if (empty($productIds)) {
            return [
                'frequency' => $frequency,
                'labels' => [],
                'buckets' => [],
                'series' => [],
            ];
        }

        $frequency = $frequency === 'monthly' ? 'monthly' : 'weekly';
        $metric = $metric === 'revenue' ? 'revenue' : 'units';
        $periods = max(1, $periods);
        $periods = min(
            $periods,
            $frequency === 'weekly' ? self::MAX_WEEKLY_PERIODS : self::MAX_MONTHLY_PERIODS
        );

        if ($frequency === 'weekly') {
            $end = Carbon::now()->endOfWeek();
            $start = $end->copy()->subWeeks($periods - 1)->startOfWeek();
        } else {
            $end = Carbon::now()->endOfMonth();
            $start = $end->copy()->subMonths($periods - 1)->startOfMonth();
        }

        $buckets = $this->buildBuckets($start, $end, $frequency);
        $bucketKeys = array_keys($buckets);

        $seriesData = [];
        foreach ($productIds as $productId) {
            $seriesData[$productId] = array_fill_keys($bucketKeys, 0);
        }

        $rows = $this->baseOrderItemQuery($start, $end)
            ->whereIn('order_items.product_id', $productIds)
            ->select(
                'order_items.product_id',
                DB::raw('DATE(orders.created_at) as sale_date'),
                DB::raw('SUM(order_items.quantity) as total_units'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('order_items.product_id', DB::raw('DATE(orders.created_at)'))
            ->orderBy(DB::raw('DATE(orders.created_at)'))
            ->get();

        $valueKey = $metric === 'revenue' ? 'total_revenue' : 'total_units';

        foreach ($rows as $row) {
            $saleDate = Carbon::parse($row->sale_date);
            $bucketKey = $frequency === 'weekly'
                ? $saleDate->copy()->startOfWeek()->format('Y-m-d')
                : $saleDate->copy()->startOfMonth()->format('Y-m-d');

            if (isset($seriesData[$row->product_id][$bucketKey])) {
                $value = $metric === 'revenue'
                    ? (float) $row->$valueKey
                    : (int) $row->$valueKey;

                $seriesData[$row->product_id][$bucketKey] += $value;
            }
        }

        $products = Product::withTrashed()
            ->with('productCategoryRS:id,name')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $series = [];
        foreach ($productIds as $productId) {
            $product = $products->get($productId);
            $series[] = [
                'product_id' => $productId,
                'name' => $product?->name ?? "Product {$productId}",
                'category_name' => $product?->productCategoryRS?->name,
                'data' => array_values(array_map(
                    fn($value) => $metric === 'revenue'
                        ? round((float) $value, 2)
                        : (int) $value,
                    $seriesData[$productId] ?? array_fill(0, count($bucketKeys), 0)
                )),
            ];
        }

        return [
            'frequency' => $frequency,
            'labels' => array_map(fn($bucket) => $bucket['label'], $buckets),
            'buckets' => array_values($buckets),
            'series' => $series,
        ];
    }

    public function getUpcomingWeekForecast(): array
    {
        $currentWeekStart = Carbon::now()->copy()->startOfWeek();
        $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();
        $nextWeekStart = $currentWeekStart->copy()->addWeek();
        $nextWeekEnd = $currentWeekEnd->copy()->addWeek();

        $weekStructure = $this->buildWeekStructure($nextWeekStart);
        $weekdayKeys = array_column($weekStructure, 'key');

        $currentWeekValues = array_fill_keys($weekdayKeys, 0.0);

        $rows = $this->baseOrderItemQuery($currentWeekStart, $currentWeekEnd)
            ->select(
                DB::raw('DATE(orders.created_at) as sale_date'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->get();

        foreach ($rows as $row) {
            $weekday = Carbon::parse($row->sale_date)->format('D');
            if (array_key_exists($weekday, $currentWeekValues)) {
                $currentWeekValues[$weekday] += round((float) $row->total_revenue, 2);
            }
        }

        $totalRevenue = array_sum($currentWeekValues);
        $daysWithSales = count(array_filter($currentWeekValues, fn($value) => $value > 0));
        $divisor = $daysWithSales > 0 ? $daysWithSales : count($currentWeekValues);
        $averageDailyRevenue = $divisor > 0 ? $totalRevenue / $divisor : 0.0;

        $forecastValues = [];
        foreach ($weekdayKeys as $weekday) {
            $historical = $currentWeekValues[$weekday] ?? 0.0;
            $baseLine = $historical > 0 ? $historical : $averageDailyRevenue;

            $forecastValues[] = round($baseLine * 1.05, 2);
        }

        return [
            'label' => $this->formatRangeLabel($nextWeekStart, $nextWeekEnd),
            'current_range' => $this->formatRangeLabel($currentWeekStart, $currentWeekEnd),
            'upcoming_range' => $this->formatRangeLabel($nextWeekStart, $nextWeekEnd),
            'labels' => array_column($weekStructure, 'label'),
            'series' => [
                [
                    'name' => 'Forecast (Next Week)',
                    'data' => $forecastValues,
                ],
                [
                    'name' => 'Current Week (Actual)',
                    'data' => array_values($currentWeekValues),
                ],
            ],
            'dashArray' => [0, 8],
            'metadata' => [
                'averageDailyRevenue' => round($averageDailyRevenue, 2),
                'totalCurrentWeekRevenue' => round($totalRevenue, 2),
            ],
        ];
    }

    protected function normalizeLimit(?int $limit): int
    {
        $limit = $limit ?? self::DEFAULT_LIMIT;

        if ($limit <= 0) {
            return self::DEFAULT_LIMIT;
        }

        return min($limit, self::MAX_LIMIT);
    }

    protected function fetchCategorySales(Carbon $start, Carbon $end): Collection
    {
        $query = $this->baseOrderItemQuery($start, $end)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
            ->whereNull('products.deleted_at')
            ->whereNull('product_categories.deleted_at')
            ->select(
                'product_categories.id as category_id',
                'product_categories.name as category_name',
                'products.id as product_id',
                'products.name as product_name',
                'products.image as product_image',
                DB::raw('SUM(order_items.quantity) as total_units'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy(
                'product_categories.id',
                'product_categories.name',
                'products.id',
                'products.name',
                'products.image'
            )
            ->orderByDesc(DB::raw('SUM(order_items.quantity)'));

        return $query->get();
    }

    protected function transformCategoryResults(Collection $results, int $limit): array
    {
        return $results
            ->groupBy('category_id')
            ->map(function (Collection $items) use ($limit) {
                $items = $items->sortByDesc(fn($item) => (int) $item->total_units);

                $topItems = $items
                    ->take($limit)
                    ->values()
                    ->map(function ($item, int $index) {
                        $totalUnits = (int) $item->total_units;
                        $totalRevenue = (float) $item->total_revenue;

                        return [
                            'rank' => $index + 1,
                            'product_id' => (int) $item->product_id,
                            'product_name' => $item->product_name,
                            'total_units' => $totalUnits,
                            'total_revenue' => round($totalRevenue, 2),
                            'average_unit_price' => $totalUnits > 0
                                ? round($totalRevenue / $totalUnits, 2)
                                : 0.0,
                            'image' => $item->product_image,
                        ];
                    })
                    ->filter(fn($item) => $item['total_units'] > 0)
                    ->values()
                    ->map(function (array $item, int $index) {
                        $item['rank'] = $index + 1;
                        return $item;
                    })
                    ->values();

                if ($topItems->isEmpty()) {
                    return null;
                }

                return [
                    'category_id' => (int) $items->first()->category_id,
                    'category_name' => $items->first()->category_name,
                    'total_units' => (int) $items->sum(fn($item) => (int) $item->total_units),
                    'total_revenue' => round(
                        $items->sum(fn($item) => (float) $item->total_revenue),
                        2
                    ),
                    'items' => $topItems->toArray(),
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    protected function baseOrderItemQuery(Carbon $start, Carbon $end)
    {
        $startDateTime = $start->copy()->startOfDay();
        $endDateTime = $end->copy()->endOfDay();

        return OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNull('order_items.deleted_at')
            ->whereNull('orders.deleted_at')
            ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
            ->whereIn('orders.status', $this->completedStatuses);
    }

    protected function buildBuckets(Carbon $start, Carbon $end, string $frequency): array
    {
        $buckets = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            if ($frequency === 'weekly') {
                $bucketStart = $cursor->copy();
                $bucketEnd = $cursor->copy()->endOfWeek();
                if ($bucketEnd->gt($end)) {
                    $bucketEnd = $end->copy();
                }
            } else {
                $bucketStart = $cursor->copy();
                $bucketEnd = $cursor->copy()->endOfMonth();
                if ($bucketEnd->gt($end)) {
                    $bucketEnd = $end->copy();
                }
            }

            $key = $bucketStart->format('Y-m-d');

            $buckets[$key] = [
                'key' => $key,
                'label' => $this->formatRangeLabel($bucketStart, $bucketEnd),
                'start_date' => $bucketStart->toDateString(),
                'end_date' => $bucketEnd->toDateString(),
            ];

            $cursor = $frequency === 'weekly'
                ? $cursor->addWeek()
                : $cursor->addMonth();
        }

        return $buckets;
    }

    protected function buildWeekStructure(Carbon $weekStart): array
    {
        $structure = [];
        $cursor = $weekStart->copy()->startOfDay();
        $weekEnd = $weekStart->copy()->endOfWeek();

        while ($cursor <= $weekEnd) {
            $structure[] = [
                'key' => $cursor->format('D'),
                'date' => $cursor->toDateString(),
                'label' => sprintf(
                    '%s (%s)',
                    $cursor->format('M d'),
                    $cursor->format('D')
                ),
            ];

            $cursor->addDay();
        }

        return $structure;
    }

    protected function formatRangeLabel(Carbon $start, Carbon $end): string
    {
        if ($start->isSameDay($end)) {
            return $start->format('M d, Y');
        }

        if ($start->isSameMonth($end) && $start->isSameYear($end)) {
            return sprintf(
                '%s - %s, %s',
                $start->format('M d'),
                $end->format('d'),
                $end->format('Y')
            );
        }

        if ($start->isSameYear($end)) {
            return sprintf(
                '%s - %s, %s',
                $start->format('M d'),
                $end->format('M d'),
                $end->format('Y')
            );
        }

        return sprintf(
            '%s - %s',
            $start->format('M d, Y'),
            $end->format('M d, Y')
        );
    }
}

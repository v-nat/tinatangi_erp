<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * BestSellerHistorySeeder
 *
 * Seeds 2 years of POS order history (March 2024 – March 2026) to
 * power the executive best-sellers dashboard. Generates realistic
 * daily sales with tiered product popularity, seasonal patterns,
 * and weekday/weekend traffic differences.
 *
 * Run with: php artisan db:seed --class=BestSellerHistorySeeder
 *
 * Prerequisites: UserSeeder, ProductSeeder
 */
class BestSellerHistorySeeder extends Seeder
{
    // ── Status codes ────────────────────────────────────────────────────────────
    private const COMPLETED = 23;

    // ── Seasonal demand multipliers ──────────────────────────────────────────────
    private const SEASON_MULT = [
        1  => 1.20,  // Jan – post-holiday rush
        2  => 1.15,  // Feb – Valentine's Day
        3  => 1.00,  // Mar – base
        4  => 0.88,  // Apr – slow season
        5  => 0.92,  // May
        6  => 1.10,  // Jun – summer start
        7  => 1.18,  // Jul – summer peak
        8  => 1.05,  // Aug
        9  => 0.95,  // Sep
        10 => 1.08,  // Oct – ber months
        11 => 1.22,  // Nov – pre-holiday
        12 => 1.45,  // Dec – holiday peak
    ];

    // ── Order type and payment distributions ─────────────────────────────────────
    // Dine-in weighted heavier for a café/restaurant
    private const ORDER_TYPES  = ['dine-in', 'dine-in', 'dine-in', 'take-out', 'take-out', 'delivery'];
    private const PAY_METHODS  = ['cash', 'cash', 'cash', 'card', 'card'];

    // ── Product popularity tier weights ──────────────────────────────────────────
    // A product's tier determines how often it appears in a weighted draw.
    // Tier 1 (top 20% of products)  → weight 12  ← clear best-sellers
    // Tier 2 (next 30%)             → weight  5
    // Tier 3 (next 30%)             → weight  2
    // Tier 4 (bottom 20%)           → weight  1  ← rarely ordered
    private const TIER_WEIGHT = [1 => 12, 2 => 5, 3 => 2, 4 => 1];

    // ── Daily order volume ────────────────────────────────────────────────────────
    private const WEEKDAY_BASE = 30;
    private const WEEKEND_BASE = 50;

    public function run(): void
    {
        // ── Load reference data ──────────────────────────────────────────────────
        $users    = DB::table('users')->pluck('id')->toArray();
        $products = DB::table('products')
            ->select('id', 'base_price', 'product_category_id')
            ->orderBy('id')
            ->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Run ProductSeeder first.');
            return;
        }

        if (empty($users)) {
            $this->command->warn('No users found. Run UserSeeder first.');
            return;
        }

        // ── Assign popularity tiers ───────────────────────────────────────────────
        // Deterministic: lower product IDs are assigned higher tiers so the same
        // products always rank as best-sellers regardless of run order.
        $productArr = $products->toArray();
        $total      = count($productArr);

        foreach ($productArr as $idx => &$p) {
            $pct    = ($idx + 1) / $total;
            $p->tier = match (true) {
                $pct <= 0.20 => 1,
                $pct <= 0.50 => 2,
                $pct <= 0.80 => 3,
                default       => 4,
            };
        }
        unset($p);

        // ── Build a weighted draw pool ────────────────────────────────────────────
        // Each product appears N times in the pool equal to its tier weight.
        // A deterministic crc32 shuffle per order gives reproducible "random" picks.
        $weightedPool = [];
        foreach ($productArr as $p) {
            $weight = self::TIER_WEIGHT[$p->tier];
            for ($w = 0; $w < $weight; $w++) {
                $weightedPool[] = $p;
            }
        }

        // ── Starting IDs (offset from existing rows) ─────────────────────────────
        $orderId     = (int) (DB::table('orders')->max('id')        ?? 0) + 1;
        $orderItemId = (int) (DB::table('order_items')->max('id')   ?? 0) + 1;
        $salesRptId  = (int) (DB::table('sales_reports')->max('id') ?? 0) + 1;

        // Use first and last users as reporter / reviewer for sales reports
        $reporterId = $users[0];
        $reviewerId = $users[count($users) - 1];

        $orders     = [];
        $orderItems = [];
        $salesRpts  = [];

        // ── Date range: 2 years back from today ──────────────────────────────────
        $cursor = Carbon::now()->subYears(2)->startOfDay();
        $end    = Carbon::now()->endOfDay();

        $this->command->info('Generating 2 years of POS order history…');

        while ($cursor->lte($end)) {
            $month      = $cursor->month;
            $seasonMult = self::SEASON_MULT[$month];
            $isWeekend  = $cursor->isWeekend();

            $base      = $isWeekend ? self::WEEKEND_BASE : self::WEEKDAY_BASE;
            $numOrders = max(5, (int) round($base * $seasonMult));

            for ($i = 0; $i < $numOrders; $i++) {
                // Deterministic seed: unique per order so shuffle is reproducible
                $seed = crc32('order' . $orderId . $cursor->toDateString() . $i);

                // Spread orders across operating hours (10:00 – 21:00)
                $hour   = 10 + (abs($seed) % 12);
                $minute = abs(crc32($seed . 'm')) % 60;
                $orderTime = $cursor->copy()->setTime($hour, $minute);

                // Pick user, order type, payment method
                $userId    = $users[abs($seed) % count($users)];
                $orderType = self::ORDER_TYPES[abs(crc32($seed . 't')) % count(self::ORDER_TYPES)];
                $payMethod = self::PAY_METHODS[abs(crc32($seed . 'p'))  % count(self::PAY_METHODS)];

                // ── Select products for this order ────────────────────────────────
                // Number of distinct items: 1–4, skewed toward 2–3
                $numItems = match (abs($seed) % 10) {
                    0       => 1,
                    1, 2    => 4,
                    default => 2 + (abs($seed) % 2),  // 2 or 3 → covers cases 3-9
                };

                // Shuffle the weighted pool deterministically, then pick $numItems unique products
                $pool = $weightedPool;
                usort($pool, static fn($a, $b) => crc32($seed . $a->id) <=> crc32($seed . $b->id));

                $seen      = [];
                $lineItems = [];

                foreach ($pool as $prod) {
                    if (isset($seen[$prod->id])) {
                        continue;
                    }
                    $seen[$prod->id] = true;

                    // Tier-1 products can sell 1–3 units; others sell 1
                    $qty = ($prod->tier === 1)
                        ? max(1, abs(crc32($seed . $prod->id . 'q')) % 3 + 1)
                        : 1;

                    $lineItems[] = [
                        'product_id' => $prod->id,
                        'unit_price' => (float) $prod->base_price,
                        'quantity'   => $qty,
                    ];

                    if (count($lineItems) >= $numItems) {
                        break;
                    }
                }

                // Safety: guarantee at least 1 line item
                if (empty($lineItems)) {
                    $p         = $productArr[0];
                    $lineItems = [[
                        'product_id' => $p->id,
                        'unit_price' => (float) $p->base_price,
                        'quantity'   => 1,
                    ]];
                }

                // ── Compute subtotals and order total ─────────────────────────────
                $orderTotal = 0.0;
                foreach ($lineItems as &$li) {
                    $li['subtotal'] = round($li['unit_price'] * $li['quantity'], 2);
                    $orderTotal    += $li['subtotal'];
                }
                unset($li);
                $orderTotal = round($orderTotal, 2);

                // ── Orders row ────────────────────────────────────────────────────
                $orders[] = [
                    'id'                   => $orderId,
                    'order_id'             => null,
                    'user_id'              => $userId,
                    'total_amount'         => $orderTotal,
                    'status'               => self::COMPLETED,
                    'order_type'           => $orderType,
                    'payment_method'       => $payMethod,
                    'payment_status'       => 'paid',
                    'discount_id'          => null,
                    'discount_amount'      => 0,
                    'gov_discount_type_id' => null,
                    'gov_discount_amount'  => 0,
                    'created_at'           => $orderTime->toDateTimeString(),
                    'updated_at'           => $orderTime->toDateTimeString(),
                ];

                // ── Order items rows ──────────────────────────────────────────────
                foreach ($lineItems as $li) {
                    $orderItems[] = [
                        'id'         => $orderItemId++,
                        'order_id'   => $orderId,
                        'product_id' => $li['product_id'],
                        'quantity'   => $li['quantity'],
                        'unit_price' => $li['unit_price'],
                        'subtotal'   => $li['subtotal'],
                        'notes'      => null,
                        'created_at' => $orderTime->toDateTimeString(),
                        'updated_at' => $orderTime->toDateTimeString(),
                    ];
                }

                // ── Sales report row ──────────────────────────────────────────────
                $salesRpts[] = [
                    'id'           => $salesRptId++,
                    'order_id'     => $orderId,
                    'total_amount' => $orderTotal,
                    'status'       => self::COMPLETED,
                    'reported_by'  => $reporterId,
                    'reported_at'  => $orderTime->copy()->addMinutes(5)->toDateTimeString(),
                    'reviewed_by'  => $reviewerId,
                    'reviewed_at'  => $orderTime->copy()->addHour()->toDateTimeString(),
                    'remarks'      => null,
                    'created_at'   => $orderTime->toDateTimeString(),
                    'updated_at'   => $orderTime->copy()->addHour()->toDateTimeString(),
                ];

                $orderId++;
            }

            $cursor->addDay();
        }

        // ── Bulk-insert in chunks to avoid memory / packet limits ────────────────
        $this->command->info(sprintf(
            'Inserting %d orders, %d order items, %d sales reports…',
            count($orders), count($orderItems), count($salesRpts)
        ));

        foreach (array_chunk($orders, 500) as $chunk) {
            DB::table('orders')->insert($chunk);
        }
        foreach (array_chunk($orderItems, 1000) as $chunk) {
            DB::table('order_items')->insert($chunk);
        }
        foreach (array_chunk($salesRpts, 500) as $chunk) {
            DB::table('sales_reports')->insert($chunk);
        }

        $this->command->info('✓ Best-seller history seeded successfully.');
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Orders',        number_format(count($orders))],
                ['Order Items',   number_format(count($orderItems))],
                ['Sales Reports', number_format(count($salesRpts))],
                ['Date Range',    Carbon::now()->subYears(2)->format('Y-m') . ' → ' . Carbon::now()->format('Y-m')],
                ['Tier-1 Products (best-sellers)', count(array_filter($productArr, fn($p) => $p->tier === 1))],
            ]
        );
    }
}

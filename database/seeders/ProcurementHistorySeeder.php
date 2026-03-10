<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ProcurementHistorySeeder
 *
 * Seeds 5 years of procurement history (March 2021 – March 2026) to
 * demonstrate the spend-forecasting dashboard. Generates realistic
 * monthly purchase cycles with year-over-year growth and seasonal
 * demand patterns.
 *
 * Run with: php artisan db:seed --class=ProcurementHistorySeeder
 *
 * Prerequisites: CategorySeeder, ItemSeeder, SupplierSeeder, EmployeeSeeder
 */
class ProcurementHistorySeeder extends Seeder
{
    // ── Status codes ────────────────────────────────────────────────────────────
    private const COMPLETED = 23;
    private const APPROVED  = 13;
    private const PENDING   = 11;

    // ── Year-over-year growth multipliers (reflects business expansion) ─────────
    private const YEAR_MULT = [
        2021 => 1.00,
        2022 => 1.18,
        2023 => 1.38,
        2024 => 1.62,
        2025 => 1.88,
        2026 => 2.05,
    ];

    // ── Seasonal demand multipliers ──────────────────────────────────────────────
    // Higher in holiday season (Dec) and summer (Jun-Jul), lower in lean months
    private const SEASON_MULT = [
        1  => 1.20,  // Jan – post-holiday surge
        2  => 1.10,  // Feb – Valentines
        3  => 1.00,  // Mar – base
        4  => 0.92,  // Apr – slow
        5  => 0.95,  // May
        6  => 1.12,  // Jun – summer
        7  => 1.15,  // Jul – mid-year
        8  => 1.05,  // Aug
        9  => 1.00,  // Sep
        10 => 1.08,  // Oct – ber months
        11 => 1.20,  // Nov – pre-holiday
        12 => 1.45,  // Dec – holiday peak
    ];

    public function run(): void
    {
        // ── Load reference data ─────────────────────────────────────────────────
        $items     = DB::table('items')->select('id', 'category_id', 'unit_price')->get();
        $suppliers = DB::table('suppliers')->pluck('id')->toArray();
        $employees = DB::table('employees')->pluck('id')->toArray();

        if ($items->isEmpty() || empty($suppliers) || empty($employees)) {
            $this->command->warn('Missing base data. Ensure ItemSeeder, SupplierSeeder, and EmployeeSeeder ran first.');
            return;
        }

        // ── Categorised item pools ──────────────────────────────────────────────
        // Pool A → raw materials (coffee, bakery, kitchen) — high-frequency monthly restocks
        $poolRaw = $items->whereIn('category_id', [1])->values();

        // Pool B → consumables (cups, filters) + packaging — monthly restocks
        $poolCons = $items->whereIn('category_id', [2, 4])->values();

        // Pool C → equipment & tools + cleaning/sanitation — bi-monthly
        $poolEquip = $items->whereIn('category_id', [5, 6])->values();

        // Pool D → dining, cold storage, uniforms — quarterly
        $poolMisc = $items->whereIn('category_id', [3, 7, 8])->values();

        if ($poolRaw->isEmpty() || $poolCons->isEmpty()) {
            $this->command->warn('No raw-material or consumable items found. Run ItemSeeder first.');
            return;
        }

        // ── Starting IDs (offset from any existing data) ────────────────────────
        $prId  = (int) (DB::table('purchase_requests')->max('id')       ?? 0) + 1;
        $poId  = (int) (DB::table('purchase_orders')->max('id')         ?? 0) + 1;
        $podId = (int) (DB::table('purchase_order_details')->max('id')  ?? 0) + 1;
        $poRef = $poId; // used for human-readable PO reference strings

        $prs  = [];
        $pos  = [];
        $pods = [];

        // ── Supplier mapping ────────────────────────────────────────────────────
        // Supplier slot 0 → Premium Coffee Roasters (raw materials)
        // Supplier slot 1 → Cup & Lid Supplies (consumables / packaging)
        // Supplier slot 2 → Equipment & Tools Trading (equipment / cleaning)
        $sup = static function (int $slot) use ($suppliers): int {
            return $suppliers[$slot % count($suppliers)];
        };

        $emp = static function (int $slot) use ($employees): int {
            return $employees[$slot % count($employees)];
        };

        // ── Generate historical completed orders (Mar 2021 – Mar 2026) ──────────
        $cursor = Carbon::create(2021, 3, 1)->startOfMonth();
        $end    = Carbon::now()->startOfMonth();

        while ($cursor->lte($end)) {
            $year  = $cursor->year;
            $month = $cursor->month;
            $mult  = (self::YEAR_MULT[$year] ?? 1.0) * (self::SEASON_MULT[$month] ?? 1.0);

            // Spread orders across the month to avoid clustering on day 1
            $day1 = min(3  + ($month % 5),      27);
            $day2 = min(12 + ($month % 6),      27);
            $day3 = min(20 + ($month % 4),      27);

            // ─ Order A: Raw materials — every month ─────────────────────────────
            [$prId, $poId, $podId, $poRef] = $this->addOrder(
                $prs, $pos, $pods,
                $prId, $poId, $podId, $poRef,
                $poolRaw, $sup(0), $emp(0),
                $cursor->copy()->setDay($day1),
                self::COMPLETED, $mult, 4
            );

            // ─ Order B: Consumables / packaging — every month ───────────────────
            [$prId, $poId, $podId, $poRef] = $this->addOrder(
                $prs, $pos, $pods,
                $prId, $poId, $podId, $poRef,
                $poolCons, $sup(1), $emp(1),
                $cursor->copy()->setDay($day2),
                self::COMPLETED, $mult, 3
            );

            // ─ Order C: Equipment & cleaning — every even month ─────────────────
            if ($month % 2 === 0 && $poolEquip->isNotEmpty()) {
                [$prId, $poId, $podId, $poRef] = $this->addOrder(
                    $prs, $pos, $pods,
                    $prId, $poId, $podId, $poRef,
                    $poolEquip, $sup(2), $emp(2),
                    $cursor->copy()->setDay($day3),
                    self::COMPLETED, $mult * 0.65, 3
                );
            }

            // ─ Order D: Misc supplies — every quarter (Mar, Jun, Sep, Dec) ──────
            if ($month % 3 === 0 && $poolMisc->isNotEmpty()) {
                [$prId, $poId, $podId, $poRef] = $this->addOrder(
                    $prs, $pos, $pods,
                    $prId, $poId, $podId, $poRef,
                    $poolMisc, $sup(1), $emp(3),
                    $cursor->copy()->setDay($day3),
                    self::COMPLETED, $mult * 0.45, 3
                );
            }

            $cursor->addMonth();
        }

        // ── Add live/active orders for the KPI dashboard demo ──────────────────
        $today = Carbon::now();

        // 2 Pending PRs (status 11) — awaiting approval
        foreach ([3, 6] as $daysAgo) {
            [$prId, $poId, $podId, $poRef] = $this->addOrder(
                $prs, $pos, $pods,
                $prId, $poId, $podId, $poRef,
                $poolRaw, $sup(0), $emp(0),
                $today->copy()->subDays($daysAgo),
                self::PENDING, 2.05, 4
            );
        }

        // 1 Approved PO (status 13) with a past delivery date → shows as overdue
        [$prId, $poId, $podId, $poRef] = $this->addOrder(
            $prs, $pos, $pods,
            $prId, $poId, $podId, $poRef,
            $poolCons, $sup(1), $emp(1),
            $today->copy()->subDays(9),
            self::APPROVED, 2.05, 3,
            $today->copy()->subDays(4) // delivery_date in the past → overdue
        );

        // 1 Approved PO (open, not yet overdue)
        [$prId, $poId, $podId, $poRef] = $this->addOrder(
            $prs, $pos, $pods,
            $prId, $poId, $podId, $poRef,
            $poolEquip, $sup(2), $emp(2),
            $today->copy()->subDays(2),
            self::APPROVED, 2.05, 2,
            $today->copy()->addDays(5) // delivery_date in the future
        );

        // ── Bulk-insert ─────────────────────────────────────────────────────────
        $this->command->info(sprintf(
            'Inserting %d purchase requests, %d purchase orders, %d line items…',
            count($prs), count($pos), count($pods)
        ));

        foreach (array_chunk($prs, 100) as $chunk) {
            DB::table('purchase_requests')->insert($chunk);
        }
        foreach (array_chunk($pos, 100) as $chunk) {
            DB::table('purchase_orders')->insert($chunk);
        }
        foreach (array_chunk($pods, 200) as $chunk) {
            DB::table('purchase_order_details')->insert($chunk);
        }

        $this->command->info('✓ Procurement history seeded successfully.');
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Purchase Requests', count($prs)],
                ['Purchase Orders',   count($pos)],
                ['Line Items',        count($pods)],
                ['Date Range',        '2021-03 → ' . Carbon::now()->format('Y-m')],
            ]
        );
    }

    // ── Helper: build one purchase-request → purchase-order → detail chain ──────
    /**
     * @param  array<int, array<string, mixed>>  $prs
     * @param  array<int, array<string, mixed>>  $pos
     * @param  array<int, array<string, mixed>>  $pods
     * @return array{int, int, int, int}  [$prId, $poId, $podId, $poRef]
     */
    private function addOrder(
        array &$prs,
        array &$pos,
        array &$pods,
        int $prId,
        int $poId,
        int $podId,
        int $poRef,
        \Illuminate\Support\Collection $itemPool,
        int $supplierId,
        int $employeeId,
        Carbon $orderDate,
        int $status,
        float $mult,
        int $numItems,
        ?Carbon $overrideDelivery = null
    ): array {
        if ($itemPool->isEmpty()) {
            return [$prId, $poId, $podId, $poRef];
        }

        $count = min($numItems, $itemPool->count());

        // Semi-deterministic item selection: avoids true randomness so the
        // spending pattern is reproducible, while still varying by month/order.
        $poolArr = $itemPool->toArray();
        $seed    = crc32($prId . $orderDate->toDateString());
        usort($poolArr, static fn($a, $b) => crc32($seed . $a->id) <=> crc32($seed . $b->id));
        $selected = array_slice($poolArr, 0, $count);

        $deliveryDate = $overrideDelivery ?? $orderDate->copy()->addDays(5);
        $orderTotal   = 0.0;

        foreach ($selected as $item) {
            // Base quantity scaled to the item's unit price so spend stays realistic
            $unitPrice = (float) $item->unit_price;

            $baseQty = match (true) {
                $unitPrice >= 10_000 => 1,           // coffee machines, ovens, etc.
                $unitPrice >= 2_000  => 2,           // refrigerators, freezers
                $unitPrice >= 500    => random_int(5,  15),
                $unitPrice >= 100    => random_int(10, 30),
                default              => random_int(25, 60),  // cheap consumables
            };

            // Equipment items (cat 5) don't scale with business volume
            $qty   = ($item->category_id === 5)
                ? $baseQty
                : max(1, (int) round($baseQty * $mult));
            $total = round($qty * $unitPrice, 2);
            $orderTotal += $total;

            $pods[] = [
                'purchase_order_id' => $poId,
                'item_id'           => $item->id,
                'category_id'       => $item->category_id,
                'quantity'          => $qty,
                'unit_price'        => $unitPrice,
                'total_amount'      => $total,
                'backorder_qnty'    => 0,
                'delivered_qnty'    => ($status === self::COMPLETED) ? $qty : 0,
                'sku'               => 'SKU-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                'status'            => $status,
                'created_at'        => $orderDate->toDateTimeString(),
                'updated_at'        => $deliveryDate->toDateTimeString(),
            ];
            $podId++;
        }

        $prs[] = [
            'id'              => $prId,
            'type'            => 'restock',
            'requested_date'  => $orderDate->toDateTimeString(),
            'department'      => 1,
            'amount'          => round($orderTotal, 2),
            'requested_by_id' => $employeeId,
            'remarks'         => null,
            'invoice_id'      => null,
            'supplier_id'     => $supplierId,
            'status'          => $status,
            'created_at'      => $orderDate->toDateTimeString(),
            'updated_at'      => $deliveryDate->toDateTimeString(),
        ];

        $pos[] = [
            'id'                     => $poId,
            'type'                   => 'restock',
            'purchase_orderId'       => null,
            'order_date'             => $orderDate->toDateTimeString(),
            'expected_delivery_date' => $deliveryDate->toDateTimeString(),
            'delivery_date'          => $deliveryDate->toDateTimeString(),
            'delivery_name'          => 'Standard Delivery',
            'remarks'                => null,
            'purchase_request_id'    => $prId,
            'created_by_id'          => $employeeId,
            'supplier_id'            => $supplierId,
            'status'                 => $status,
            'created_at'             => $orderDate->toDateTimeString(),
            'updated_at'             => $deliveryDate->toDateTimeString(),
        ];

        return [$prId + 1, $poId + 1, $podId, $poRef + 1];
    }
}

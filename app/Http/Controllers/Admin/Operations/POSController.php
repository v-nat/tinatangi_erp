<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductCategory;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\InventoryItem;
use App\Models\StockTransaction;
use App\Enums\TransactionType;
use Illuminate\Support\Str;
use App\Models\Announcement;
use App\Services\BestSellerService;

class POSController extends Controller
{
    protected BestSellerService $bestSellerService;

    public function __construct(BestSellerService $bestSellerService)
    {
        $this->bestSellerService = $bestSellerService;
    }

    private function getProductsByCategory($categoryName = null)
    {
        try {
            $query = Product::where('deleted_at', null);

            if ($categoryName) {
                $query->whereHas('productCategoryRS', function ($q) use ($categoryName) {
                    $q->where('name', $categoryName);
                });
            }

            $products = $query->with('productCategoryRS', 'ingredients')
                ->select('id', 'name', 'base_price', 'image', 'servings', 'product_category_id')
                ->get()
                ->map(function (Product $product) {
                    $availableServings = $product->syncAvailability();

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'base_price' => (float) $product->base_price,
                        'image' => $product->image,
                        'servings' => $product->servings,
                        'available_servings' => $availableServings,
                        'product_category_id' => $product->product_category_id,
                    ];
                });

            return response()->json(['data' => $products]);
        } catch (\Exception $e) {
            Log::error('Error fetching products for POS: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load products.'], 500);
        }
    }

    public function getAllProducts()
    {
        return $this->getProductsByCategory(null);
    }

    public function getPastriesProducts()
    {
        return $this->getProductsByCategory('Pastries');
    }

    public function getBeveragesProducts()
    {
        return $this->getProductsByCategory('Beverages');
    }

    public function getMealsProducts()
    {
        return $this->getProductsByCategory('Meals');
    }

    public function getSnacksAndSidesProducts()
    {
         return $this->getProductsByCategory('Snacks & Sides');
    }

    public function getProductCategories()
    {
        try {
            $categories = ProductCategory::orderBy('name')
                ->get(['id', 'name'])
                ->map(function (ProductCategory $category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => Str::slug($category->name),
                    ];
                });

            return response()->json(['data' => $categories]);
        } catch (\Exception $e) {
            Log::error('Error fetching product categories for POS: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load product categories.'], 500);
        }
    }

    public function getProducts(Request $request)
    {
        $categoryName = $request->query('category');

        if (is_null($categoryName) || strtolower($categoryName) === 'all') {
            return $this->getProductsByCategory(null);
        }

        return $this->getProductsByCategory($categoryName);
    }

    public function getGovernmentDiscountTypes()
    {
        $types = \App\Models\GovernmentDiscountType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'percentage', 'description']);

        return response()->json(['data' => $types]);
    }

    public function getActiveDiscounts()
    {
        $today = Carbon::today()->toDateString();

        $discounts = Announcement::where('type', 'discount')
            ->where('status', 35)
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', $today);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereRaw('usage_count < usage_limit');
            })
            ->with('products:id')
            ->get(['id', 'title', 'discount_type', 'discount_value', 'min_spend', 'applicable_to', 'usage_limit', 'usage_count'])
            ->map(function ($d) {
                return array_merge($d->toArray(), [
                    'product_ids' => $d->products->pluck('id')->values(),
                ]);
            });

        return response()->json(['data' => $discounts]);
    }

    public function submitOrder(Request $request)
    {
        $validatedData = $request->validate([
            'order_items'              => 'required|array',
            'order_items.*.product_id' => 'required|integer|exists:products,id',
            'order_items.*.quantity'   => 'required|integer|min:1',
            'grand_total'              => 'required|numeric|min:0',
            'order_type'               => 'required|string|in:dine-in,take-out',
            'cash_received'            => 'required|numeric|min:0',
            'change_due'               => 'required|numeric|min:0',
            'discount_id'              => 'nullable|integer|exists:announcements,id',
            'gov_discount_type_id'     => 'nullable|integer|exists:government_discount_types,id',
        ]);

        $orderItems = $validatedData['order_items'];
        $submittedGrandTotal = $validatedData['grand_total'];
        $orderType = $validatedData['order_type'];

        DB::beginTransaction();

        try {
            $productIds = collect($orderItems)->pluck('product_id')->unique();
            $products = Product::with('ingredients')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            if ($products->count() !== $productIds->count()) {
                throw new \Exception('One or more products could not be found.');
            }

            $requestedQuantities = collect($orderItems)
                ->groupBy('product_id')
                ->map(fn ($items) => $items->sum('quantity'));

            foreach ($requestedQuantities as $productId => $totalQuantity) {
                $product = $products->get($productId);
                $availableServings = $product->calculateAvailableServings();

                if ($availableServings < $totalQuantity) {
                    throw new \Exception("Insufficient servings for {$product->name}. Available: {$availableServings}, requested: {$totalQuantity}.");
                }
            }

            $calculatedGrandTotal = 0;
            $itemsToSave = [];

            foreach ($orderItems as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $product = $products->get($productId);

                if (! $product) {
                    throw new \Exception("Product ID {$productId} not found.");
                }

                $dbUnitPrice = (float) $product->base_price;
                $itemTotal = $dbUnitPrice * $quantity;
                $calculatedGrandTotal += $itemTotal;

                $itemsToSave[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $dbUnitPrice,
                    'subtotal' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (abs($submittedGrandTotal - $calculatedGrandTotal) > 0.01) {
                throw new \Exception("Grand total mismatch. Client total: {$submittedGrandTotal}, Server total: {$calculatedGrandTotal}");
            }

            $today = Carbon::today()->toDateString();
            $lastOrder = Order::whereDate('created_at', $today)
                ->where('order_id', 'like', 'ORD-%')
                ->orderBy('id', 'desc')
                ->first();

            $nextNum = 100;
            if ($lastOrder && preg_match('/^ORD-(\d{4})$/', $lastOrder->order_id, $matches)) {
                $nextNum = (int) $matches[1] + 1;
            }

            $newOrderId = 'ORD-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            // ── Discount calculation ──────────────────────────────────────
            $discountId     = $validatedData['discount_id'] ?? null;
            $totalDiscount  = 0.0;

            if ($discountId) {
                $discount = Announcement::with('products')->find($discountId);

                if ($discount &&
                    $discount->type   === 'discount' &&
                    $discount->status === 35 &&
                    (is_null($discount->usage_limit) || $discount->usage_count < $discount->usage_limit)
                ) {
                    $qualifyingProductIds = $discount->applicable_to === 'specific'
                        ? $discount->products->pluck('id')->toArray()
                        : null; // null = all products

                    $qualifyingSubtotal = 0.0;
                    foreach ($itemsToSave as $item) {
                        if ($qualifyingProductIds === null || in_array($item['product_id'], $qualifyingProductIds)) {
                            $qualifyingSubtotal += $item['subtotal'];
                        }
                    }

                    if ($discount->discount_type === 'percentage') {
                        $totalDiscount = round($qualifyingSubtotal * ((float) $discount->discount_value / 100), 2);
                    } else {
                        // fixed: cap at qualifying subtotal
                        $totalDiscount = min((float) $discount->discount_value, $qualifyingSubtotal);
                    }
                } else {
                    $discountId = null; // discount no longer valid — ignore
                }
            }

            // ── Government discount calculation ──────────────────────────
            $govDiscountTypeId = $validatedData['gov_discount_type_id'] ?? null;
            $govDiscountAmount = 0.0;

            if ($govDiscountTypeId) {
                $govType = \App\Models\GovernmentDiscountType::where('id', $govDiscountTypeId)
                    ->where('is_active', true)
                    ->first();
                if ($govType) {
                    $govDiscountAmount = round($calculatedGrandTotal * ($govType->percentage / 100), 2);
                } else {
                    $govDiscountTypeId = null; // type no longer active — ignore
                }
            }
            // ─────────────────────────────────────────────────────────────

            $order = Order::create([
                'order_id'             => $newOrderId,
                'user_id'              => auth('')->id(),
                'total_amount'         => max(0, $calculatedGrandTotal - $totalDiscount - $govDiscountAmount),
                'status'               => 28,
                'order_type'           => $orderType,
                'payment_method'       => 'Cash',
                'payment_status'       => 'Paid',
                'discount_id'          => $discountId,
                'discount_amount'      => $totalDiscount,
                'gov_discount_type_id' => $govDiscountTypeId,
                'gov_discount_amount'  => $govDiscountAmount,
            ]);

            $order->items()->createMany($itemsToSave);

            if ($discountId) {
                Announcement::where('id', $discountId)->increment('usage_count');
            }

            $affectedInventoryIds = [];

            foreach ($orderItems as $item) {
                $product = $products->get($item['product_id']);

                if ($product && $product->ingredients->isNotEmpty()) {
                    foreach ($product->ingredients as $ingredient) {
                        $inventoryItem = InventoryItem::whereKey($ingredient->id)->lockForUpdate()->first();

                        if (! $inventoryItem) {
                            throw new \Exception('Inventory item not found for ingredient.');
                        }

                        $quantityUsedPerProduct = (float) $ingredient->pivot->quantity_used;

                        if ($quantityUsedPerProduct <= 0) {
                            continue;
                        }

                        $totalQuantityToDeduct = $quantityUsedPerProduct * $item['quantity'];
                        $availableBaseStock = $inventoryItem->getAvailableBaseUnits();

                        if ($availableBaseStock < $totalQuantityToDeduct) {
                            throw new \Exception("Insufficient stock for: " . optional($inventoryItem->itemss)->name);
                        }

                        $oldBaseQuantity = $availableBaseStock;
                        $newBaseQuantity = $availableBaseStock - $totalQuantityToDeduct;

                        $inventoryItem->setBaseStockFromQuantity($newBaseQuantity);
                        $inventoryItem->save();

                        $affectedInventoryIds[] = $inventoryItem->id;

                        StockTransaction::create([
                            'transaction_type' => TransactionType::OUT,
                            'quantity' => $totalQuantityToDeduct,
                            'old_qnty' => $oldBaseQuantity,
                            'transaction_date' => now(),
                            'reference_type' => 'Sale',
                            'reference_id' => $inventoryItem->id,
                            'user_id' => auth('')->id(),
                            'status' => 23,
                        ]);
                    }
                }
            }

            if (! empty($affectedInventoryIds)) {
                InventoryItem::whereIn('id', array_unique($affectedInventoryIds))
                    ->get()
                    ->each
                    ->refreshProductAvailability();
            }

            DB::commit();

            return response()->json([
                'message' => 'Order completed successfully.',
                'order_id' => $order->order_id,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order submission failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Order submission failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function recentOrders()
    {
        try {
            $today = Carbon::today()->toDateString();
            $orders = Order::whereDate('created_at', $today)
                ->with(['items.productRS', 'userRS'])
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_id' => $order->order_id,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'quantity' => $item->quantity,
                            'product_name' => $item->productRS ? $item->productRS->name : 'Unknown',
                        ];
                    }),
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'total_amount' => $order->total_amount,
                    'order_type' => ucwords(str_replace('-', ' ', $order->order_type)),
                    'payment_method' => $order->payment_method,
                    'cashier_name' => $order->userRS ? $order->userRS->full_name : 'N/A',
                    'status' => Status::getStatusText($order->status),
                ];
            });

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            Log::error('Error fetching recent orders: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load recent orders.'], 500);
        }
    }

    public function voidOrder(Order $order)
    {
        if ($order->status == 28 || $order->status == 29) {

            DB::beginTransaction();
            try {
                $order->status = 31;
                $order->save();

                $affectedInventoryIds = [];

                foreach ($order->items as $item) {
                    $product = Product::with('ingredients')->find($item->product_id);
                    $orderQuantity = $item->quantity;

                    if ($product && $product->ingredients->isNotEmpty()) {
                        foreach ($product->ingredients as $ingredient) {
                            $inventoryItem = InventoryItem::whereKey($ingredient->id)->lockForUpdate()->first();

                            if (! $inventoryItem) {
                                continue;
                            }

                            $quantityUsedPerProduct = (float) $ingredient->pivot->quantity_used;

                            if ($quantityUsedPerProduct <= 0) {
                                continue;
                            }

                            $totalQuantityToReturn = $quantityUsedPerProduct * $orderQuantity;

                            $oldBaseQuantity = $inventoryItem->base_unit_stock_level ?? $inventoryItem->stock_level;
                            $oldQuantity = $inventoryItem->stock_level;

                            if (! is_null($inventoryItem->base_unit_stock_level)) {
                                $inventoryItem->base_unit_stock_level += $totalQuantityToReturn;
                            }

                            $inventoryItem->stock_level += $totalQuantityToReturn;
                            $inventoryItem->save();

                            $affectedInventoryIds[] = $inventoryItem->id;

                            StockTransaction::create([
                                'transaction_type' => TransactionType::IN,
                                'quantity' => $totalQuantityToReturn,
                                'old_qnty' => $oldBaseQuantity,
                                'transaction_date' => now(),
                                'reference_type' => 'Void Sale',
                                'reference_id' => $inventoryItem->id,
                                'user_id' => auth('')->id(),
                                'status' => 23,
                            ]);
                        }
                    }
                }

                if (! empty($affectedInventoryIds)) {
                    InventoryItem::whereIn('id', array_unique($affectedInventoryIds))
                        ->get()
                        ->each
                        ->refreshProductAvailability();
                }

                DB::commit();
                return response()->json(['message' => 'Order #' . $order->order_id . ' has been voided. Stock returned.']);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error voiding order: ' . $e->getMessage());
                return response()->json(['message' => 'Failed to void order.'], 500);
            }

        } else if ($order->status == 31) {
            return response()->json(['message' => 'This order has already been voided.'], 400);
        } else {
            return response()->json(['message' => 'This order cannot be voided at its current status.'], 400);
        }
    }

    public function monthlyBestSellers(Request $request)
    {
        $limit = (int) $request->query('limit', 5);

        if ($limit <= 0) {
            $limit = 5;
        }

        $limit = min($limit, 10);

        try {
            $bestSellerData = $this->bestSellerService->getMonthlyBestSellers($limit);

            $topItems = collect($bestSellerData['categories'] ?? [])
                ->flatMap(fn ($category) => $category['items'] ?? [])
                ->sortByDesc(fn ($item) => (int) ($item['total_units'] ?? 0))
                ->take($limit)
                ->values();

            if ($topItems->isEmpty()) {
                return response()->json(['data' => []]);
            }

            $productIds = $topItems->pluck('product_id')->all();

            $products = Product::whereIn('id', $productIds)
                ->with('productCategoryRS')
                ->select('id', 'name', 'base_price', 'image', 'servings', 'product_category_id')
                ->get()
                ->keyBy('id');

            $orderedProducts = $topItems
                ->map(function ($item) use ($products) {
                    $product = $products->get($item['product_id']);

                    if (! $product) {
                        return null;
                    }

                    $availableServings = $product->syncAvailability();

                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'base_price' => (float) $product->base_price,
                        'image' => $product->image,
                        'servings' => $product->servings,
                        'available_servings' => $availableServings,
                        'product_category_id' => $product->product_category_id,
                        'total_units' => (int) ($item['total_units'] ?? 0),
                    ];
                })
                ->filter()
                ->values();

            return response()->json(['data' => $orderedProducts]);
        } catch (\Exception $e) {
            Log::error('Error fetching monthly best sellers for POS: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to load monthly best sellers.'], 500);
        }
    }
}

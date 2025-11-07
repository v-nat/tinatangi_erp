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

class POSController extends Controller
{
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
                ->get();

            $products = $products->map(function ($product) {

                $canMakeOne = true;
                if ($product->ingredients->isNotEmpty()) {
                    foreach ($product->ingredients as $ingredient) {
                        $quantityNeededForOne = $ingredient->pivot->quantity_used;
                        if ($ingredient->stock_level < $quantityNeededForOne) {
                            $canMakeOne = false;
                            break;
                        }
                    }
                }

                if (!$canMakeOne) {
                    $product->servings = 0;
                }

                unset($product->ingredients);
                return $product;
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

    public function submitOrder(Request $request)
    {
        $validatedData = $request->validate([
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|integer|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'grand_total' => 'required|numeric|min:0',
            'order_type' => 'required|string|in:dine-in,take-out',
            'cash_received' => 'required|numeric|min:0',
            'change_due' => 'required|numeric|min:0',
        ]);

        $orderItems = $validatedData['order_items'];
        $submittedGrandTotal = $validatedData['grand_total'];
        $orderType = $validatedData['order_type'];

        DB::beginTransaction();

        try {
            $productIds = collect($orderItems)->pluck('product_id')->unique();
            $products = Product::whereIn('id', $productIds)->pluck('base_price', 'id');

            $calculatedGrandTotal = 0;
            $itemsToSave = [];

            foreach ($orderItems as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $dbUnitPrice = $products->get($productId);

                if (is_null($dbUnitPrice)) {
                    throw new \Exception("Product ID {$productId} not found or price is missing.");
                }

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

            $order = Order::create([
                'order_id' => 'ORD-' . time() . '-' . rand(100, 999),
                'user_id' => auth('')->id(),
                'total_amount' => $calculatedGrandTotal,
                'status' => 28,
                'order_type' => $orderType,
                'payment_method' => 'Cash',
                'payment_status' => 'Paid',
            ]);

            $order->items()->createMany($itemsToSave);

            foreach ($orderItems as $item) {
                $product = Product::with('ingredients')->find($item['product_id']);

                if ($product && $product->ingredients->isNotEmpty()) {
                    foreach ($product->ingredients as $ingredient) {
                        $inventoryItem = InventoryItem::find($ingredient->id);

                        if ($inventoryItem) {
                            $quantityUsedPerProduct = $ingredient->pivot->quantity_used;

                            $totalQuantityToDeduct = $quantityUsedPerProduct * $item['quantity'];

                            $oldQuantity = $inventoryItem->stock_level;

                            if ($inventoryItem->stock_level < $totalQuantityToDeduct) {
                                throw new \Exception("Insufficient stock for: " . optional($inventoryItem->itemss)->name);
                            }

                            $inventoryItem->decrement('stock_level', $totalQuantityToDeduct);

                            StockTransaction::create([
                                'transaction_type' => TransactionType::OUT,
                                'quantity' => $totalQuantityToDeduct,
                                'old_qnty' => $oldQuantity,
                                'transaction_date' => now(),
                                'reference_type' => 'Sale',
                                'reference_id' => $inventoryItem->id,
                                'user_id' => auth('')->id(),
                                'status' => 23,
                            ]);
                        }
                    }
                }
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

                foreach ($order->items as $item) {
                    $product = Product::with('ingredients')->find($item->product_id);
                    $orderQuantity = $item->quantity;

                    if ($product && $product->ingredients->isNotEmpty()) {
                        foreach ($product->ingredients as $ingredient) {
                            $inventoryItem = InventoryItem::find($ingredient->id);
                            if ($inventoryItem) {
                                $quantityUsedPerProduct = $ingredient->pivot->quantity_used;

                                $totalQuantityToReturn = $quantityUsedPerProduct * $orderQuantity;

                                $oldQuantity = $inventoryItem->stock_level;

                                $inventoryItem->increment('stock_level', $totalQuantityToReturn);

                                StockTransaction::create([
                                    'transaction_type' => TransactionType::IN,
                                    'quantity' => $totalQuantityToReturn,
                                    'old_qnty' => $oldQuantity,
                                    'transaction_date' => now(),
                                    'reference_type' => 'Void Sale',
                                    'reference_id' => $inventoryItem->id,
                                    'user_id' => auth('')->id(),
                                    'status' => 23,
                                ]);
                            }
                        }
                    }
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
}

<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Controllers\GenerateIdController;

class POSController extends Controller
{
    public function getAllProducts()
    {
        try {
            $allProducts = Product::all()->where('status', 1);

            return response()->json([
                'data' => $allProducts->map(function ($p) {
                    return [
                        'id'                    => $p->id,
                        'name'                  => $p->name ?? 'N/A',
                        'base_price'            => $p->base_price ?? 'N/A',
                        'product_category_id'   => $p->product_category_id ?? 'N/A',
                        'image'                 => $p->image ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getPastriesProducts()
    {
        try {
            $pastriesCategory = ProductCategory::where('name', 'Pastries')->first();

            if (!$pastriesCategory) {
                return response()->json(['data' => []], 200);
            }

            $pastriesId = $pastriesCategory->id;

            $pastriesProducts = Product::where('status', 1)
                ->where('product_category_id', $pastriesId)
                ->get();

            return response()->json([
                'data' => $pastriesProducts->map(function ($p) {
                    return [
                        'id'                    => $p->id,
                        'name'                  => $p->name ?? 'N/A',
                        'base_price'            => $p->base_price ?? 'N/A',
                        'product_category_id'   => $p->product_category_id ?? 'N/A',
                        'image'                 => $p->image ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getBeveragesProducts()
    {
        try {
            $beveragesCategory = ProductCategory::where('name', 'Beverages')->first();

            if (!$beveragesCategory) {
                return response()->json(['data' => []], 200);
            }

            $beveragesId = $beveragesCategory->id;

            $beveragesProducts = Product::where('status', 1)
                ->where('product_category_id', $beveragesId)
                ->get();

            return response()->json([
                'data' => $beveragesProducts->map(function ($p) {
                    return [
                        'id'                    => $p->id,
                        'name'                  => $p->name ?? 'N/A',
                        'base_price'            => $p->base_price ?? 'N/A',
                        'product_category_id'   => $p->product_category_id ?? 'N/A',
                        'image'                 => $p->image ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMealsProducts()
    {
        try {
            $mealsCategory = ProductCategory::where('name', 'Meals')->first();

            if (!$mealsCategory) {
                return response()->json(['data' => []], 200);
            }

            $mealsId = $mealsCategory->id;

            $mealsProducts = Product::where('status', 1)
                ->where('product_category_id', $mealsId)
                ->get();

            return response()->json([
                'data' => $mealsProducts->map(function ($p) {
                    return [
                        'id'                    => $p->id,
                        'name'                  => $p->name ?? 'N/A',
                        'base_price'            => $p->base_price ?? 'N/A',
                        'product_category_id'   => $p->product_category_id ?? 'N/A',
                        'image'                 => $p->image ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSnacksAndSidesProducts()
    {
        try {
            $category = ProductCategory::where('name', 'Snacks & Sides')->first();

            if (!$category) {
                $category = ProductCategory::where('name', 'Pastries')->first();
            }

            if (!$category) {
                return response()->json(['data' => []], 200);
            }

            $categoryId = $category->id;

            $products = Product::where('status', 1)
                ->where('product_category_id', $categoryId)
                ->get();

            return response()->json([
                'data' => $products->map(function ($p) {
                    return [
                        'id'                    => $p->id,
                        'name'                  => $p->name ?? 'N/A',
                        'base_price'            => $p->base_price ?? 'N/A',
                        'product_category_id'   => $p->product_category_id ?? 'N/A',
                        'image'                 => $p->image ?? 'N/A',
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function submitOrder(StoreOrderRequest $request)
    {
        $orderItems = $request->validated('order_items');
        $submittedGrandTotal = $request->validated('grand_total');

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
                throw new \Exception("Grand total mismatch.");
            }

            $customOrderId = GenerateIdController::generateID('order');

            $order = Order::create([
                'order_id' => $customOrderId,
                'user_id' => auth('')->id(),
                'total_amount' => $calculatedGrandTotal,
                'status' => 11,
                'order_type' => 'dine-in',
                'payment_status' => 'paid',
            ]);

            $order->items()->createMany($itemsToSave);

            DB::commit();

            return response()->json([
                'message' => 'Order completed successfully.',
                'order_id' => $order->order_id,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Order submission failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

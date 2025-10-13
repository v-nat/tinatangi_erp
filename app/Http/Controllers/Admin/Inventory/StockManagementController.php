<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Item;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Enums\TransactionType;
use App\Models\PurchaseRequest;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderDetail;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GenerateIdController;

class StockManagementController extends Controller
{
    public function stockTransactions()
    {
        try {
            $transaction = StockTransaction::with(['inventoryItemRS.itemss', 'user'])
            ->orderBy('transaction_date', 'desc')->get();

            return response()->json([
                'data' => $transaction->map(function ($data) {
                    return [
                        'id'                => $data->id,
                        'type'              => $data->transaction_type,
                        'batch'             => $data->batch,
                        'date'              => $data->transaction_date,
                        'reference'         => $data->reference_type,
                        'quantity'          => $data->quantity,
                        'item'              => optional(optional($data->inventoryItemRS)->itemss)->name,
                        'receive'           => optional($data->user)->full_name,
                        // 'selling_price'     => (float)$item->selling_price, --- IGNORE ---
                        'status'            => Status::getStatusText($data->status),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function receiveInventory($id)
    {
        try {
            DB::beginTransaction();
            $purchaseRequest = PurchaseRequest::with([
                'purchaseOrders.purchaseOrderDetail',
            ])->where('id', $id)->first();

            if (!$purchaseRequest) {
                return;
            }

            $hasSku = $purchaseRequest->purchaseOrders
                ->pluck('purchaseOrderDetail')
                ->flatten()
                ->contains(function ($detail) {
                    return $detail->sku !== null;
                });

            $foundSkus = $purchaseRequest->purchaseOrders
                ->pluck('purchaseOrderDetail')
                ->flatten()
                ->filter(function ($detail) {
                    return $detail->sku !== null;
                })
                ->pluck('sku');

            if ($hasSku) {
                $sku = $foundSkus;
                $purchaseRequest->status = 23;
                $purchaseRequest->save();

                $type = strtoUpper(trim('Adj'));
                $transaction_type = TransactionType::tryFrom($type);

                $purchase_order = PurchaseOrder::where('purchase_request_id', $id)->first();
                $purchase_order->status = 23;
                $purchase_order->save();

                $po_detail = PurchaseOrderDetail::where('purchase_order_id', $purchase_order->id)->first();
                $po_detail->status = 23;
                $po_detail->save();

                $inventoryItem = InventoryItem::where('sku', $sku)->first();
                $old_qnty = $inventoryItem->stock_level;
                $receive_qnty = $po_detail->quantity;
                $new_total_amount = $receive_qnty * $po_detail->unit_price;
                $inventoryItem->stock_level += $receive_qnty;
                $inventoryItem->cost_price += $new_total_amount;
                $inventoryItem->status = 24;
                $inventoryItem->save();

                $previousBatchModel = StockTransaction::where('reference_id', $inventoryItem->id)
                    ->orderByDesc('batch')
                    ->first();

                $previousBatchNumber = $previousBatchModel ? $previousBatchModel->batch : 0;

                $stockTransaction = StockTransaction::create([
                    'transaction_type' => $transaction_type,
                    'quantity' => $receive_qnty,
                    'old_qnty' => $old_qnty,
                    'batch' => $previousBatchNumber + 1,
                    'transaction_date' => now(),
                    'reference_type' => 'Restock',
                    'reference_id' => $inventoryItem->id,
                    'user_id' => auth('')->user()->id,
                    'status' => 23,
                ]);
                $stockTransaction->save();
            } else {
                $purchaseRequest->status = 23;
                $purchaseRequest->save();

                foreach ($purchaseRequest->purchaseOrders as $order) {
                    $order->status = 23;
                    $order->save();
                    foreach ($order->purchaseOrderDetail as $detail) {
                        $item = Item::with('unitRS')->find($detail->item_id);
                        $quantity = $detail->quantity;
                        $unit_price = $detail->unit_price;

                        if ($item) {
                            $existingItem = InventoryItem::where('item_id', $item->id)->first();

                            if ($existingItem) {
                                $type = strtoUpper(trim('Adj'));
                                $transaction_type = TransactionType::tryFrom($type);

                                $inventoryItem = InventoryItem::where('item_id', $existingItem->item_id)->first();
                                $old_qnty = $inventoryItem->stock_level;
                                $receive_qnty = $quantity;
                                $new_total_amount = $receive_qnty * $unit_price;
                                $inventoryItem->stock_level += $receive_qnty;
                                $inventoryItem->cost_price += $new_total_amount;
                                $inventoryItem->status = 24;
                                $inventoryItem->save();

                                $previousBatch = StockTransaction::where('reference_id', $inventoryItem->id)
                                    ->orderByDesc('batch')
                                    ->first();
                                $previousBatchNumber = $previousBatch ? $previousBatch->batch : 0;
                                $stockTransaction = StockTransaction::create([
                                    'transaction_type' => $transaction_type,
                                    'quantity' => $receive_qnty,
                                    'old_qnty' => $old_qnty,
                                    'batch' => $previousBatchNumber + 1,
                                    'transaction_date' => now(),
                                    'reference_type' => 'Restock',
                                    'reference_id' => $inventoryItem->id,
                                    'user_id' => auth('')->user()->id,
                                    'status' => 23,
                                ]);
                                $stockTransaction->save();
                            } else {
                                $type = strtoUpper(trim('In'));
                                $transaction_type = TransactionType::tryFrom($type);

                                $inventoryItem = InventoryItem::create([
                                    'sku' => GenerateIdController::generateID('sku'),
                                    'item_id' => $item->id,
                                    'inventory_location_id' => $item->inventory_location_id,
                                    'unit_id' => $item->unitRS->id,
                                    'category_id' => $item->category_id,
                                    'cost_price' => $detail->total_amount,
                                    'stock_level' => $detail->quantity,
                                    'status' => 24,
                                ]);
                                $inventoryItem->save();
                                $stockTransaction = StockTransaction::create([
                                    'transaction_type' => $transaction_type,
                                    'quantity' => $detail->quantity,
                                    'transaction_date' => now(),
                                    'reference_type' => 'Purchase Order',
                                    'reference_id' => $inventoryItem->id,
                                    'user_id' => auth('')->user()->id,
                                    'supplier_id' => $order->supplier_id,
                                    'batch' => 1,
                                    'status' => 23,
                                ]);
                                $stockTransaction->save();
                                $detail->status = 23;
                                $detail->save();
                            }
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Inventory received and stock updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function restockRequest(Request $request)
    {
        try {
            $sku = $request->sku;
            $item_id = $request->item_id;
            $quantity = $request->qnty;

            DB::beginTransaction();

            $inventoryItem = InventoryItem::where('sku', $sku)->first();
            $inventoryItem->status = 27;
            $inventoryItem->save();

            $id = GenerateIdController::generateID('purchase_request');
            $item = Item::where('id', $item_id)->first();

            $total_amount = $item->unit_price * $quantity;

            $purchase_req = PurchaseRequest::create([
                'id' => (int)$id,
                'type' => 'Restock Request',
                'department' => 5,
                'amount' => $total_amount,
                'requested_by_id' => auth('')->user()->id,
                'requested_date' => now(),
                'remarks' => '',
                'supplier_id' => null,
                'status' => 27,
            ]);
            $purchase_req->save();
            $purchase_order = PurchaseOrder::create([
                'type' => 'Restock Request',
                'purchase_orderId' => (int)$id,
                'purchase_request_id' => $purchase_req->id,
                'order_date' => null,
                'expected_delivery_date' => null,
                'delivery_date' => null,
                'delivery_name' => null,
                'remarks' => 'pending to generate purchase request',
                'created_by_id' => auth('')->user()->id,
                'supplier_id' => null,
                'status' => 27,
            ]);
            $purchase_order->save();
            PurchaseOrderDetail::create([
                'purchase_order_id' => $purchase_order->id,
                'item_id' => $item_id,
                'category_id' => $item->category_id,
                'quantity' => $quantity,
                'unit_price' => $item->unit_price,
                'total_amount' => $total_amount,
                'backorder_qnty' => null,
                'delivered_qnty' => null,
                'sku' => $sku,
                'status' => 27,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Restock Request Submitted Successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

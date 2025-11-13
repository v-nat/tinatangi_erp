<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Item;
use App\Models\Status;
use App\Models\ItemUnit;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Enums\TransactionType;
use App\Models\UnitConversion;
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
            $transaction = StockTransaction::with(['inventoryItemRS.itemss', 'inventoryItemRS.unit', 'inventoryItemRS.baseUnit', 'user'])
                ->orderBy('transaction_date', 'desc')->get();

            return response()->json([
                'data' => $transaction->map(function ($data) {
                    $inventoryItem = $data->inventoryItemRS;
                    $unitLabel = $inventoryItem ? $inventoryItem->getDisplayUnitLabel() : '';
                    $quantityRaw = (float) $data->quantity;
                    $quantityFormatted = number_format($quantityRaw, 2);
                    $quantityDisplay = trim($quantityFormatted . ($unitLabel ? ' ' . $unitLabel : ''));
                    return [
                        'id'                => $data->id,
                        'type'              => $data->transaction_type,
                        'batch'             => $data->batch,
                        'date'              => $data->transaction_date->setTimezone('Asia/Manila')->format('Y-m-d\TH:i:s\Z'),
                        'reference'         => $data->reference_type,
                        'quantity'          => $quantityRaw,
                        'quantity_formatted'=> $quantityFormatted,
                        'quantity_display'  => $quantityDisplay,
                        'unit'              => $unitLabel,
                        'item'              => optional(optional($data->inventoryItemRS)->itemss)->name,
                        'receive'           => optional($data->user)->full_name,
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
            $purchaseRequest = PurchaseRequest::with(['purchaseOrders.purchaseOrderDetail.itemss.unit'])->findOrFail($id);

            foreach ($purchaseRequest->purchaseOrders as $order) {
                foreach ($order->purchaseOrderDetail as $detail) {

                    $purchaseUnit = $detail->itemss->unit;
                    $pricePerPurchaseUnit = $detail->unit_price;

                    $baseUnit = ItemUnit::where('type', $purchaseUnit->type)->where('is_base_unit', true)->first();

                    if ($baseUnit && $purchaseUnit->id !== $baseUnit->id) {
                        $conversion = UnitConversion::where('from_unit_id', $purchaseUnit->id)
                            ->where('to_unit_id', $baseUnit->id)
                            ->first();
                    }

                    $inventoryItem = InventoryItem::firstOrNew(['item_id' => $detail->item_id]);

                    $old_stock = $inventoryItem->stock_level ?? 0;
                    $old_stock_base_unit = $inventoryItem->base_unit_stock_level ?? 0;
                    $old_total_value = $old_stock * $inventoryItem->cost_price ?? 0;

                    $received_quantity = $detail->quantity;
                    $received_quantity_in_base_unit = $received_quantity;
                    if (isset($conversion) && $baseUnit && $purchaseUnit->id !== $baseUnit->id) {
                        $received_quantity_in_base_unit = $received_quantity * $conversion->factor;
                    }

                    $new_stock = $old_stock + $received_quantity;
                    $new_stock_base_unit = $old_stock_base_unit + $received_quantity_in_base_unit;

                    $received_total_value = $received_quantity * $pricePerPurchaseUnit;

                    $new_total_value = $old_total_value + $received_total_value;

                    if (!$inventoryItem->unit_cost > 0.00000) {
                        $qty = 1;
                        if (isset($conversion) && $baseUnit && $purchaseUnit->id !== $baseUnit->id) {
                            $qty = $qty * $conversion->factor;
                        }
                        $price = Item::where('id', $detail->item_id)->value('unit_price');
                        $new_unit_cost = $price / $qty;

                        $inventoryItem->unit_cost = $new_unit_cost;
                    }

                    $inventoryItem->stock_level = $new_stock;
                    $inventoryItem->base_unit_stock_level = $new_stock_base_unit;
                    $inventoryItem->cost_price = $new_total_value;

                    if (!$inventoryItem->exists) {
                        $inventoryItem->sku = GenerateIdController::generateID('sku');
                        $inventoryItem->item_id = $detail->item_id;
                        $inventoryItem->inventory_location_id = $detail->itemss->inventory_location_id;
                        $inventoryItem->unit_id = $purchaseUnit->id;
                        $inventoryItem->base_unit_id = $baseUnit->id;
                        $inventoryItem->category_id = $detail->itemss->category_id;
                    }

                    $inventoryItem->status = 24;
                    $inventoryItem->save();
                    $inventoryItem->refreshProductAvailability();
                    ////////////////////////////////////////////////////////////////////////////////////////////
                    $previousBatchModel = StockTransaction::where('reference_id', $inventoryItem->id)
                        ->orderByDesc('batch')
                        ->first();

                    $previousBatchNumber = $previousBatchModel ? $previousBatchModel->batch : 0;
                    $type = ($previousBatchNumber === 0) ? 'Purchase Order' : 'Restock';

                    StockTransaction::create([
                        'transaction_type' => $old_stock > 0 ? TransactionType::ADJ : TransactionType::IN,
                        'quantity' => $received_quantity,
                        'old_qnty' => $old_stock,
                        'batch' => $previousBatchNumber + 1,
                        'transaction_date' => now(),
                        'reference_type' => $type,
                        'reference_id' => $inventoryItem->id,
                        'user_id' => auth('')->user()->id,
                        'status' => 23,
                    ]);
                    ///////////////////////////////////////////////////////////////////////////////////////////////
                    $detail->status = 23;
                    $detail->save();
                }
                $order->status = 23;
                $order->save();
            }

            $purchaseRequest->status = 23;
            $purchaseRequest->save();

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

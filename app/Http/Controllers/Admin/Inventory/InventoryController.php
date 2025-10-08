<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\GenerateIdController;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Status;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Enums\transaction_type;
use App\Enums\TransactionType;
use App\Models\PurchaseRequest;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderDetail;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index()
    {
        return view("pages.admin.inventory.index",);
    }

    public function all()
    {
        return view("pages.admin.inventory.all-items");
    }

    public function fetchDataToDisplay()
    {
        $toReceiveCount = PurchaseRequest::where('status', 16)->count();
        $itemsInStock = InventoryItem::sum('stock_level');
        $inventoryItems = InventoryItem::get(['id', 'stock_level', 'status']);

        $lowStockCount = 0;

        $outOfStockIds = [];
        $lowStockIds = [];

        foreach ($inventoryItems as $item) {
            $totalStocks = StockTransaction::where('reference_id', $item->id)->value('quantity');

            $totalStocks = $totalStocks ?? 1;

            $stockMargin = $totalStocks * 0.30;

            if ($item->stock_level <= 0) {
                if ($item->status != 26) {
                    $outOfStockIds[] = $item->id;
                }
            }
            elseif ($item->stock_level <= $stockMargin) {
                if ($item->status != 25) {
                    $lowStockIds[] = $item->id;
                }
                $lowStockCount++;
            }
        }

        if (!empty($outOfStockIds)) {
            InventoryItem::whereIn('id', $outOfStockIds)->update(['status' => 26]);
        }

        if (!empty($lowStockIds)) {
            InventoryItem::whereIn('id', $lowStockIds)
                ->where('status', '!=', 26)
                ->update(['status' => 25]);
        }

        $outOfStockCount = InventoryItem::where('stock_level', 0)->count();

        return response()->json([
            'to_receive' => $toReceiveCount,
            'total_stocks' => $itemsInStock,
            'low_stocks' => $lowStockCount,
            'out_of_stock' => $outOfStockCount,
        ]);
    }

    public function getRecentItems()
    {
        try {
            $items = InventoryItem::with(['itemss', 'category', 'unit', 'itemStatus'])->latest()->take(10)->get();

            return response()->json([
                'data' => $items->map(function ($item) {
                    return [
                        'id'                => $item->id,
                        'sku'               => $item->sku,
                        'item_name'         => optional($item->itemss)->name,
                        'category'          => optional($item->category)->name,
                        'unit'              => optional($item->unit)->abbreviation,
                        'stock_level'       => (int)$item->stock_level,
                        'cost_price'        => (float)$item->cost_price,
                        // 'selling_price'     => (float)$item->selling_price, --- IGNORE ---
                        'status'            => Status::getStatusText($item->status),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getAllItems()
    {
        try {
            $items = InventoryItem::with(['itemss', 'category', 'unit', 'itemss.inventoryLocation','itemStatus'])->get();

            return response()->json([
                'data' => $items->map(function ($item) {
                    return [
                        'id'                => $item->id,
                        'sku'               => $item->sku,
                        'item_name'         => optional($item->itemss)->name,
                        'inventory_location'=> optional(optional($item->itemss)->inventoryLocation)->name,
                        'category'          => optional($item->category)->name,
                        'unit'              => optional($item->unit)->abbreviation,
                        'stock_level'       => (int)$item->stock_level,
                        'cost_price'        => (float)$item->cost_price,
                        // 'selling_price'     => (float)$item->selling_price, --- IGNORE ---
                        'status'            => Status::getStatusText($item->status),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getToReceive()
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.purchaseOrderDetail',
                'purchaseOrders.supplierRS',
                'purchaseOrders.purchaseOrderDetail.itemss',
                'purchaseOrders.purchaseOrderDetail.itemss.unitRS',
                'statusRS',
                'employeeRS',
                'supplierRS',
                'deptRS',
            ])->where('status', 16)->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        $mappedDetails = $order->purchaseOrderDetail->map(function ($detail) {
                            return [
                                'item_name'   => optional($detail->itemss)->name,
                                'item_unit'   => optional(optional($detail->itemss)->unitRS)->abbreviation,
                                'item_unit_name'   => optional(optional($detail->itemss)->unitRS)->name,
                                'quantity'    => (int)$detail->quantity,
                                'unit_price'  => (float)$detail->unit_price,
                                'total_amount' => (float)$detail->total_amount,
                            ];
                        });

                        return [
                            'purchase_order_id' => $order->purchase_orderId,

                            'details'           => $mappedDetails,
                        ];
                    });

                    return [
                        'id'             => $request_data->id,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'department'     => optional($request_data->deptRS)->name,
                        'remarks'        => $request_data->remarks,
                        'status'         => Status::statusAlert($request_data->status),
                        'total_amount'   => (float)$request_data->amount,
                        'invoice_id'     => $request_data->invoice_id,
                        'supplier_name'     => optional($request_data->supplierRS)->supplier_name,
                        'purchase_orders' => $mappedOrders,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getToRestock()
    {
        try {
            $forRestock = InventoryItem::with([
                'itemss',
                'unit',
                'category'
            ])->where('status', 25)->orWhere('status', 26)
                ->orderBy('stock_level', 'asc')
                ->get();

            return response()->json([
                'data' => $forRestock->map(function ($item) {
                    return [
                        'id'                => $item->id,
                        'sku'               => $item->sku,
                        'item_name'         => optional($item->itemss)->name,
                        'category'          => optional($item->category)->name,
                        'unit'              => optional($item->unit)->abbreviation,
                        'stock_level'       => (int)$item->stock_level,
                        'cost_price'        => (float)$item->cost_price,
                        'status'            => Status::getStatusText($item->status),
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
            $purchaseRequest = PurchaseRequest::findOrFail($id);
            $purchaseRequest->status = 23;
            $purchaseRequest->save();

            $type = strtoUpper(trim('In'));
            $transaction_type = TransactionType::tryFrom($type);

            foreach ($purchaseRequest->purchaseOrders as $order) {
                $order->status = 23;
                $order->save();
                foreach ($order->purchaseOrderDetail as $detail) {
                    $item = Item::with('unitRS')->find($detail->item_id);
                    if ($item) {
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
                            'reference_type' => 'PO',
                            'reference_id' => $inventoryItem->id,
                            'user_id' => auth('')->user()->id,
                            'status' => 23,
                        ]);
                        $stockTransaction->save();
                        $detail->status = 23;
                        $detail->save();
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
}

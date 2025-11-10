<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Status;
use App\Models\InventoryItem;
use App\Models\PurchaseRequest;
use App\Models\StockTransaction;
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
    public function transactionsView()
    {
        return view("pages.admin.inventory.stock-transactions");
    }

    public function fetchDataToDisplay()
    {
        $toReceiveCount = PurchaseRequest::where('status', 16)->count();
        $itemsInStock = InventoryItem::distinct()->count('item_id');
        $inventoryItems = InventoryItem::get(['id', 'stock_level', 'status']);

        $lowStockCount = 0;

        $outOfStockIds = [];
        $lowStockIds = [];

        foreach ($inventoryItems as $item) {
            $totalStocks = StockTransaction::where('reference_id', $item->id)->value('quantity');

            $totalStocks = $totalStocks ?? 1;

            $stockMargin = $totalStocks * 0.30;

            if ($item->stock_level <= 0) {
                if ($item->status != 26 && $item->status != 27) {
                    $outOfStockIds[] = $item->id;
                }
            } elseif ($item->stock_level <= $stockMargin) {
                if ($item->status != 25 && $item->status != 27) {
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
                        'stock_level'       => $item->stock_level,
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
            $items = InventoryItem::with(['itemss', 'category', 'unit', 'itemss.inventoryLocation', 'itemStatus'])->get();

            return response()->json([
                'data' => $items->map(function ($item) {
                    return [
                        'id'                => $item->id,
                        'sku'               => $item->sku,
                        'item_name'         => optional($item->itemss)->name,
                        'inventory_location' => optional(optional($item->itemss)->inventoryLocation)->name,
                        'category'          => optional($item->category)->name,
                        'unit'              => optional($item->unit)->abbreviation,
                        'stock_level'       => $item->stock_level,
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
                        'item_id'           => $item->item_id,
                        'category'          => optional($item->category)->name,
                        'unit'              => optional($item->unit)->name,
                        'stock_level'       => (int)$item->stock_level,
                        'cost_price'        => (float)$item->cost_price,
                        'status'            => Status::getStatusText($item->status),

                        'unit_price'        => optional($item->itemss)->unit_price,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

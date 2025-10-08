<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Item;
use App\Models\Status;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrderDetail;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseRequest::all()->where('status', 16)->count();
        return view("pages.admin.inventory.index", compact('purchaseOrders'));
    }

    public function all()
    {
        return view("pages.admin.inventory.all-items");
    }

    public function getToReceive()
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.purchaseOrderDetail',
                'purchaseOrders.supplierRS',
                'purchaseOrders.purchaseOrderDetail.itemss',
                'purchaseOrders.purchaseOrderDetail.itemss.unit',
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
                                'item_unit'   => optional(optional($detail->itemss)->unit)->abbreviation,
                                'item_unit_name'   => optional(optional($detail->itemss)->unit)->name,
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
}

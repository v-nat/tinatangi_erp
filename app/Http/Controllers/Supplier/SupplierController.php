<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Status;

class SupplierController extends Controller
{
    public function approveRequest()
    {
        return view('pages.supplier.approve-purchase');
    }

    public function purchaseOrdersList()
    {
        try {
            $purchaseRequests = PurchaseRequest::with([
                'purchaseOrders',
                'purchaseOrders.supplierRS',
                'statusRS',
                'employeeRS',
                'deptRS',
            ])->orderBy('updated_at', 'desc')->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    // --- 1. Map the Purchase Orders (Collection) ---
                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        // Return the individual Purchase Order object
                        return [
                            'purchase_order_id' => $order->purchase_orderId,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,
                            'supplier_name'     => optional($order->supplierRS)->supplier_name,
                        ];
                    });

                    // --- 2. Return the main Purchase Request object ---
                    return [
                        'id'             => $request_data->id,
                        'type'           => $request_data->type,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'remarks'        => $request_data->remarks,
                        'status'         => Status::getStatusText($request_data->status),
                        'total_amount'   => (float)$request_data->amount,

                        'purchase_orders' => $mappedOrders,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}

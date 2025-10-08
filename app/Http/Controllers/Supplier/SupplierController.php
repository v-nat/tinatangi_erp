<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
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
                'supplierRS',
                'deptRS',
            ])->where('supplier_id', auth('')->user()->id)
            ->orderBy('updated_at', 'desc')->get();

            return response()->json([
                'data' => $purchaseRequests->map(function ($request_data) {

                    $mappedOrders = $request_data->purchaseOrders->map(function ($order) {

                        return [
                            'purchase_order_id' => $order->purchase_orderId,
                            'order_date' => $order->order_date,
                            'delivery_date' => $order->delivery_date,

                        ];
                    });

                    return [
                        'id'             => $request_data->id,
                        'type'           => $request_data->type,
                        'requested_date' => $request_data->requested_date,
                        'requested_by_id'   => optional(optional($request_data->employeeRS)->userRS)->full_name,
                        'remarks'        => $request_data->remarks,
                        'status'         => Status::getStatusText($request_data->status),
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

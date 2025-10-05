<?php

namespace App\Http\Controllers\Admin\Procurement;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Http\Controllers\Controller;

class ProcurementController extends Controller
{
    //
    public function index()
    {
        return view("pages.admin.procurement.index");
    }

    public function createPR()
    {
        return view("pages.admin.procurement.create-purchase-request");
    }
    public function supplier()
    {
        return view("pages.admin.procurement.manage-supplier");
    }
    public function purchaseOrders()
    {
        return view("pages.admin.procurement.purchase-orders");
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
            ])->orderBy('requested_date', 'desc')->get();

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

    public function generatePurchaseOrderID()
    {
        $order_id = "";
        $year = Carbon::now()->format('Y');
        do {
            $random = rand(10000, 99999);
            $order_id = $year . $random;
        } while (PurchaseRequest::pluck('id')->contains($order_id));
        return response()->json([
            'order_id' => $order_id,
        ]);
    }
    public static function generateID($type): string
    {
        $order_id = "";
        $year = Carbon::now()->format('Y');

        if ($type == 'invoice') {
            do {
                $random = rand(10000, 99999);
                $order_id = $year . $random;
            } while (PurchaseRequest::pluck('id')->contains($order_id));
        } else if ($type == 'delivery_no'){
            do {
                $random = rand(10000, 99999);
                $order_id = $year . $random;
            } while (Invoice::pluck('delivery_no')->contains($order_id));
        }

        return $order_id;
    }
}

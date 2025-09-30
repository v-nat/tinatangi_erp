<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            $requests = PurchaseOrder::with(['employeeRS', 'statusRS', 'supplierRS'])
                ->orderBy('updated_at', 'desc')->get();

            return response()->json([
                'data' => $requests->map(function ($r) {
                    return [
                        'id'                        => $r->id,
                        'type'                      => $r->type,
                        'purchase_orderId'          => $r->purchase_orderId,
                        'order_date'                => $r->order_date ?? '',
                        'expected_delivery_date'    => $r->expected_delivery_date ?? '',
                        'delivery_date'             => $r->delivery_date ?? '',
                        'delivery_name'             => $r->delivery_name,
                        'created_by_id'             => optional(optional($r->employeeRS)->userRS)->full_name,
                        'supplier_id'               => optional($r->supplierRS)->supplier_name,
                        'remarks'                   => $r->remarks,
                        'status'                    => Status::getStatusText($r->status),
                    ];
                })
            ]);
        } catch (\Exception $e) {
            // \Log::error('Opening case fetch failed', ['error' => $e->getMessage()]);
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
}

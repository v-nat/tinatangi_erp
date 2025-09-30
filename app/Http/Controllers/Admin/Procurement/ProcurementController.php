<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
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
            $requests = PurchaseRequest::with(['employeeRS', 'deptRS', 'statusRS'])
                ->orderBy('requested_date', 'desc')->get();
                
            return response()->json([
                'data' => $requests->map(function ($r) {
                    return [
                        'id'                => $r->id,
                        'type'              => $r->type,
                        'amount'            => $r->amount,
                        'department'        => optional($r->deptRS)->name,
                        'requested_by_id'   => optional(optional($r->employeeRS)->userRS)->full_name,
                        'requested_date'      => optional(Carbon::parse($r->requested_date))->format('M d, Y'),
                        'released_by_id'   => optional(optional($r->employeeRS)->userRS)->full_name,
                        'released_date'      => optional(Carbon::parse($r->requested_date))->format('M d, Y'),
                        'remarks'             => $r->remarks,
                        'status'            => Status::getStatusText($r->status),
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

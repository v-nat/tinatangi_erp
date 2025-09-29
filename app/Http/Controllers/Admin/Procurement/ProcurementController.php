<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
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

    public function generatePurchaseOrderID()
    {
        $order_id = "";
        $year = Carbon::now()->format('Y');
        do {
            $random = rand(1000, 9999);
            $order_id = $year . $random;
        } while (PurchaseRequest::pluck('id')->contains($order_id));
        return response()->json([
            'order_id' => $order_id,
        ]);
    }
}

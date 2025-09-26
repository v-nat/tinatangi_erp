<?php

namespace App\Http\Controllers\Admin\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    //
    public function index(){
        return view("pages.admin.procurement.index");
    }

    public function createPO(){
        return view("pages.admin.procurement.create-purchase-order");
    }
    public function supplier(){
        return view("pages.admin.procurement.manage-supplier");
    }
    public function purchaseOrders(){
        return view("pages.admin.procurement.purchase-orders");
    }
}

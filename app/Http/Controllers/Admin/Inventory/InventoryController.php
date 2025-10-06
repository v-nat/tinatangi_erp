<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    //
    public function index(){
        $purchaseOrders = PurchaseRequest::all()->where('status', 16)->count();
        return view("pages.admin.inventory.index", compact('purchaseOrders'));
    }

    public function all(){
        return view("pages.admin.inventory.all-items");
    }
}

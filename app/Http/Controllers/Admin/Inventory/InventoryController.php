<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    //
    public function index(){
        return view("pages.admin.inventory.index");
    }

    public function all(){
        return view("pages.admin.inventory.all-items");
    }
}

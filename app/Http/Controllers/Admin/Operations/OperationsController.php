<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;

class OperationsController extends Controller
{
    //
    public function index() {
        return view("pages.admin.operations.dashboard");
    }
    public function pos() {
        return view("pages.admin.operations.point-of-sales");
    }
    public function kds() {
        return view('pages.admin.operations.kitchen-display');
    }
}

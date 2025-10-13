<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    //
    public function index() {
        return view("pages.admin.operations.dashboard");
    }
    public function pos() {
        return view("pages.admin.operations.point-of-sales");
    }
}

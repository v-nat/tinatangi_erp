<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OperationsController extends Controller
{
    //
    public function index() {
        return view("pages.admin.operations.dashboard");
    }
    public function pos() {
        $userPos = Auth::user()->employeeRS->empPosition->id;
        if ($userPos == 16 || $userPos == 10) {
            return view("pages.admin.operations.point-of-sales");
        }
        return redirect()->back()->with('error', "You have no access to this page.");
    }
    public function kds() {
        $userDept = strtolower(Auth::user()->employeeRS->deptRS->name);
        if ($userDept != 'kitchen department') {
            return redirect()->back()->with('error', "You have no access to this page.");
        }
        return view('pages.admin.operations.kitchen-display');
    }
}

<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function index() {
        return view('pages.admin.crm.index');
    }
}
